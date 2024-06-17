<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ファイルアップロード</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white flex flex-col items-center min-h-screen">
    <div class="w-full max-w-md mt-10">
        <form action="upload.php" method="POST" enctype="multipart/form-data" class="bg-gray-800 p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6">ファイルアップロード</h2>
            <div class="mb-4">
                <label for="audio_file" class="block text-sm font-medium text-gray-400">音楽ファイル（MP3, WAV）</label>
                <input type="file" id="audio_file" name="audio_file" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-3 text-white" accept=".mp3, .wav" required>
            </div>
            <div class="mb-4">
                <label for="lrc_file" class="block text-sm font-medium text-gray-400">LRCファイル</label>
                <input type="file" id="lrc_file" name="lrc_file" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-3 text-white" accept=".lrc" required>
            </div>
            <div class="mb-4">
                <label for="chord_file" class="block text-sm font-medium text-gray-400">コード情報ファイル（.txt）</label>
                <input type="file" id="chord_file" name="chord_file" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md py-2 px-3 text-white" accept=".txt" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md">アップロード</button>
        </form>
    </div>
</body>
</html>
