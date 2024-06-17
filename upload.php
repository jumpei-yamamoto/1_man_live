<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uploadDir = 'uploads/';
    $audioFile = $uploadDir . basename($_FILES['audio_file']['name']);
    $lrcFile = $uploadDir . basename($_FILES['lrc_file']['name']);
    $chordFile = $uploadDir . basename($_FILES['chord_file']['name']);

    $uploadOk = 1;

    // 音楽ファイルのアップロード
    if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $audioFile)) {
        echo "音楽ファイルがアップロードされました: " . htmlspecialchars(basename($_FILES['audio_file']['name'])) . "<br>";
    } else {
        echo "音楽ファイルのアップロード中にエラーが発生しました。<br>";
        $uploadOk = 0;
    }

    // LRCファイルのアップロード
    if (move_uploaded_file($_FILES['lrc_file']['tmp_name'], $lrcFile)) {
        echo "LRCファイルがアップロードされました: " . htmlspecialchars(basename($_FILES['lrc_file']['name'])) . "<br>";
    } else {
        echo "LRCファイルのアップロード中にエラーが発生しました。<br>";
        $uploadOk = 0;
    }

    // コード情報ファイルのアップロード
    if (move_uploaded_file($_FILES['chord_file']['tmp_name'], $chordFile)) {
        echo "コード情報ファイルがアップロードされました: " . htmlspecialchars(basename($_FILES['chord_file']['name'])) . "<br>";
    } else {
        echo "コード情報ファイルのアップロード中にエラーが発生しました。<br>";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        // コード情報ファイルの読み込み
        $chordData = file_get_contents($chordFile);
        $chords = json_decode($chordData, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "コード情報ファイルの解析中にエラーが発生しました: " . json_last_error_msg() . "<br>";
            exit();
        }

        // BPMと拍数に基づいたコード変更タイミングを計算
        $bpm = 130;  // 必要に応じてBPMを設定
        $secondsPerBeat = 60 / $bpm;

        // 各コードの表示タイミングを計算して追加
        $time = 0;
        foreach ($chords as &$chord) {
            $chord['time'] = $time;
            $time += $chord['beats'] * $secondsPerBeat;
        }

        // LRCファイルのパスとコード情報をセッションに保存してリダイレクト
        session_start();
        $_SESSION['audio_file'] = $audioFile;
        $_SESSION['lrc_file'] = $lrcFile;
        $_SESSION['chords'] = $chords;

        header("Location: display.php");
        exit();
    }
}
?>
