<?php
	require 'func.php';
	
	//json読み込み
	$fname = 'idyer.json';
	$json = file_get_contents($fname);
	$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
	$json = json_decode($json,true);
?>

<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=yes" >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
<link rel="stylesheet" type="text/css" href="book.css" >
<title>緯日辞典 本文</title>
</head>
<body>
<?php
	///////////////////////////////テスト用////////////////////
	$i = 0; 
	///////////////////////////////テスト用ここまで////////////////////
	$separators = array(":","+");
	//ここから表示部
	foreach($json["words"] as $entryId => $singleEntry ) {
	//ここに検索結果の繰り返し表示を入れる。
		echo '<ul class="wordEntry">';
		echo '<li class="wordForm">', $singleEntry["entry"]["form"], '</li>';
		
		$previousTitle = '';
		foreach ($singleEntry["translations"] as $index => $singleTranslation){
			if ($index === 0){
				echo '<span class="wordTitle">' , $singleTranslation["title"] , '</span>';
			}else{
				if ($previousTitle !== $singleTranslation["title"]) {
					echo '<span class="wordTitle">' , $singleTranslation["title"] , '</span>';
				}
			}
			$previousTitle = $singleTranslation["title"];
			echo '<li class="wordTrans">';
			foreach ($singleTranslation["forms"] as $singleTranslationForm){
				echo $singleTranslationForm;
				if ($singleTranslationForm !== end($singleTranslation["forms"])){
					//最後のとき以外に「、」を追加
					echo '、';
				}
			}
		}
		
		foreach ($singleEntry["contents"] as $singleContent){
			echo '<li class="wordContents">';
			echo '<span class="wordContentTitle">' , $singleContent["title"] , '</span>';
			echo hyphenate($singleContent["text"], "<wbr>", $separators);
			echo '</li>';
		}
		$relationTitles = array();
		foreach ($singleEntry["relations"] as $singleRelation){
			if (array_search($singleRelation["title"],$relationTitles) === false){
				echo '<li class="wordRelation"><span class="wordRelation">' , $singleRelation["title"] , '</span>';
				$relationTitles[] = $singleRelation["title"];
			}else{
				echo ",";
			}
			echo hyphenate($singleRelation["entry"]["form"], "<wbr>", $separators) ;
		}
		echo '</li>';
		echo '</ul>';
		///////////////////////////////テスト用////////////////////
		$i++;
		if($i === 100) {
			break;
		}
		///////////////////////////////テスト用ここまで////////////////////
	}

?>
</body>
</html>