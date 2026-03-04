<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_ENV['APP_NAME'] ?></title>
	<meta name="author" content="<?= $_ENV['APP_NAME'] ?>">
	<link rel="shortcut icon" href="<?= $asset->url('images/favicon.ico') ?>">
    <meta name="robots" content="index, follow">
    <link rel="stylesheet" href="<?= $asset->url('css/styles.css') ?>">
	
	<script src="https://cdn.tailwindcss.com"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
</head>
<body class="overflow-x-hidden">
	<main>
		<?= $content ?? '' ?>
	</main>
	<div class="group fixed bottom-0 right-0 p-3  flex items-end justify-end w-24 h-24 ">
		<a href="https://github.com/fitri-hy/CaracalPHP/tree/main/docs" target="_blank" class="cursor-pointer text-white shadow-lg flex items-center justify-center p-2 rounded-full bg-gradient-to-r from-cyan-500 to-blue-500 z-50 absolute hover:scale-105 hover:duration-300 hover:shadow-lg">
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><path fill="currentColor" d="M15.75 13a.75.75 0 0 0-.75-.75H9a.75.75 0 0 0 0 1.5h6a.75.75 0 0 0 .75-.75m0 4a.75.75 0 0 0-.75-.75H9a.75.75 0 0 0 0 1.5h6a.75.75 0 0 0 .75-.75"/><path fill="currentColor" fill-rule="evenodd" d="M7 2.25A2.75 2.75 0 0 0 4.25 5v14A2.75 2.75 0 0 0 7 21.75h10A2.75 2.75 0 0 0 19.75 19V7.968c0-.381-.124-.751-.354-1.055l-2.998-3.968a1.75 1.75 0 0 0-1.396-.695zM5.75 5c0-.69.56-1.25 1.25-1.25h7.25v4.397c0 .414.336.75.75.75h3.25V19c0 .69-.56 1.25-1.25 1.25H7c-.69 0-1.25-.56-1.25-1.25z" clip-rule="evenodd"/></svg>
		</a>
	</div>
<script src="<?= $asset->url('js/app.js') ?>"></script>
</body>
</html>