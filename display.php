<?php
session_start();

if (!isset($_SESSION['audio_file']) || !isset($_SESSION['lrc_file']) || !isset($_SESSION['chords'])) {
    echo "ファイルが正しくアップロードされていません。";
    exit();
}

$audioFile = $_SESSION['audio_file'];
$lrcFile = $_SESSION['lrc_file'];
$chords = $_SESSION['chords'];

// LRCファイルのパース関数
function parseLRC($filePath) {
    $lyrics = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $parsedLyrics = [];

    foreach ($lyrics as $line) {
        if (preg_match('/\[(\d+):(\d+\.\d+)\](.*)/', $line, $matches)) {
            $minutes = (int)$matches[1];
            $seconds = (float)$matches[2];
            $time = $minutes * 60 + $seconds;
            $text = trim($matches[3]);
            $parsedLyrics[] = ['time' => $time, 'text' => $text];
        }
    }

    return $parsedLyrics;
}

$lyrics = parseLRC($lrcFile);

// 歌詞とコードの結合
$combinedData = [];
$chordIndex = 0;

foreach ($lyrics as $lyric) {
    while ($chordIndex < count($chords) && $chords[$chordIndex]['time'] <= $lyric['time']) {
        $combinedData[] = [
            'time' => $chords[$chordIndex]['time'],
            'text' => $chords[$chordIndex]['chord'],
            'type' => 'chord'
        ];
        $chordIndex++;
    }
    $combinedData[] = [
        'time' => $lyric['time'],
        'text' => $lyric['text'],
        'type' => 'lyric'
    ];
}

// 残りのコードを追加
while ($chordIndex < count($chords)) {
    $combinedData[] = [
        'time' => $chords[$chordIndex]['time'],
        'text' => $chords[$chordIndex]['chord'],
        'type' => 'chord'
    ];
    $chordIndex++;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>音楽再生と歌詞・コード表示</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .highlight {
            color: red;
        }
        .chord {
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-900 text-white flex flex-col items-center min-h-screen">
    <div class="w-full max-w-2xl p-8 bg-gray-800 rounded-lg shadow-lg mt-8">
        <h2 class="text-2xl font-bold mb-4">音楽再生</h2>
        <audio id="audio" controls class="w-full">
            <source src="<?php echo htmlspecialchars($audioFile); ?>" type="audio/<?php echo pathinfo($audioFile, PATHINFO_EXTENSION); ?>">
            お使いのブラウザはオーディオタグをサポートしていません。
        </audio>
        <div class="mt-4">
            <button onclick="playAudio()" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md">再生</button>
            <button onclick="pauseAudio()" class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md">停止</button>
            <button onclick="resumeAudio()" class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-md">再開</button>
        </div>
        <div id="lyricsContainer" class="mt-4 text-gray-400">
            <?php foreach ($combinedData as $item): ?>
                <?php if ($item['type'] == 'chord'): ?>
                    <p data-time="<?php echo $item['time']; ?>" class="chord"><?php echo htmlspecialchars($item['text']); ?></p>
                <?php else: ?>
                    <p data-time="<?php echo $item['time']; ?>" class="lyric"><?php echo htmlspecialchars($item['text']); ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div class="mt-4 text-gray-400">
            <p>再生時間: <span id="currentTime">0</span> 秒</p>
        </div>
    </div>
    <script>
        const audio = document.getElementById('audio');
        const items = document.getElementById('lyricsContainer').children;

        function playAudio() {
            audio.play();
        }

        function pauseAudio() {
            audio.pause();
            audio.currentTime = 0;
        }

        function resumeAudio() {
            audio.play();
        }

        audio.ontimeupdate = () => {
            const currentTime = audio.currentTime;
            document.getElementById('currentTime').textContent = Math.floor(currentTime);

            for (let i = 0; i < items.length; i++) {
                let item = items[i];
                let time = parseFloat(item.getAttribute('data-time'));
                if (currentTime >= time) {
                    item.classList.add('highlight');
                } else {
                    item.classList.remove('highlight');
                }
            }
        };
    </script>
</body>
</html>
