<?php
//エスケープしてechoする関数
function echo_h($str){
    echo htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//前方一致検索
function startsWith($haystack, $needle){
	if ($needle){
		return mb_stripos($haystack, $needle, 0) === 0;
	}else{
		return false;
	}
}

//最後尾文字チェック
function endsWith($haystack, $needle){
	if ($needle){
		return substr($haystack, -strlen($needle)) === $needle;
	}else{
		return false;
	}
}

//完全一致検索
function perfectHit($haystack, $needle){
	$haystack = mb_strtolower($haystack,'UTF-8');//検索の便宜のため小文字にする
	$needle = mb_strtolower($needle,'UTF-8');//検索の便宜のため小文字にする
    return $haystack === $needle;
}

//母音で始まるかをチェック
function startsWithVowel($haystack){
	return (bool)preg_match('/^[eaoiu]/u', $haystack);
}

//母音で終わるかチェック
function endsWithVowel($haystack){
	return (bool)preg_match('/[eaoiu]$/u', $haystack);
}

//変音記号以外の記号を削除
function deleteNonIdyerinCharacters($string){
	$string = preg_replace('/[-\(\)\#]/u', '', $string);
	return $string;
}

//頭文字の連濁
function initialVoicing($string) {
	$pattern = array('/^h/u','/^k/u','/^s/u','/^t/u','/^c/u','/^p/u','/^f/u');
	$replacement = array('g','g','z','d',"d'",'b','v');
	return preg_replace($pattern, $replacement, $string);
}

//頭文字の連濁を戻す
function initialUnvoicing($string) {
	$replacement = array('/^h/u','/^k/u','/^s/u','/^t/u','/^c/u','/^p/u','/^f/u');
	$pattern = array('g','g','z','d',"d'",'b','v');
	return preg_replace($pattern, $replacement, $string);
}

//アルファベットのみで構成されているかの判定
function isDoublebyte($string) {
	return strlen($string) !== mb_strlen($string);
}

//HKS順ソート用の比較関数 作成中
//eaoiuhkstcnrmpfgzdbv
//Aを先にしたければ-1を返す。
function HKSCmp($strA,$strB){
	
	$arrHks = array("e","a","o","i","u","h","k","s","t","c","n","r","m","p","f","g","z","d","b","v");
	$odrHks = array("-120","-119","-118","-117","-116","-115","-114","-113","-112","-111","-110","-109","-108","-107","-106","-105","-104","-103","-102","-101");
	
	$strA = deleteNonIdyerinCharacters($strA);
	$strB = deleteNonIdyerinCharacters($strB);
	$strA = str_replace("\'", '', $strA);
	$strB = str_replace("\'", '', $strB);
	$strA = mb_strtolower($strA);
	$strB = mb_strtolower($strB);
	$arrA = str_split($strA);
	$arrB = str_split($strB);
	$arrA = str_replace($arrHks, $odrHks, $arrA);
	$arrB = str_replace($arrHks, $odrHks, $arrB);
	
	for ($i = 0; $i < min(mb_strlen($strA), mb_strlen($strB)); $i++ ){
		if ($arrA[$i] !== $arrB[$i]){
			return $arrA[$i] <=> $arrB[$i];
		}
		if ($i === (min(mb_strlen($strA), mb_strlen($strB))-1)){
			// 最後の文字まで同じである場合、文字列が長い方を後ろにする。
			return mb_strlen($strA) <=> mb_strlen($strB);
		}
	}
}



//ハイフネーション用
//VCCCCV→VCC-CCV
//VCCCV → VC-CCV
//VCCV → VC-CV
//VCVV → VC-VV
//VVV → VV-V
//VVC → V-VC
//CVCV → CV-CV
//配列で追加で分割したい文字列を追加できる。文字列の後での改行を許可する。
function hyphenate($string, $hyphen = "-", $extraSeparators = []){
	$original = array('!/s\'/u!','!/t\'/u!','!/n\'/u!','!/r\'/u!','!/z\'/u!','!/d\'/u!','!/S\'/u!','!/T\'/u!','!/N\'/u!','!/R\'/u!','!/Z\'/u!','!/D\'/u!');
	$temporal = array('!1!','!2!','!3!','!4!','!5!','!6!','!7!','!8!','!9!','!0!','!q!','!Q!');
	//$string = preg_replace($original, $temporal, $string);//子音を1文字にする。今はうまく機能しない
	$VCCCCV = '/([eaoiuEAOIU][hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ]{2})([hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ]{2}[eaoiuEAOIU])/u';
	$VCCCV = '/([eaoiuEAOIU][hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ])([hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ]{2}[eaoiuEAOIU])/u';
	$VCCV = '/([eaoiuEAOIU][hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ])([hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ][eaoiuEAOIU])/u';
	$VVV = '/([eaoiuEAOIU]{2})([eaoiuEAOIU])/u';
	$VVC = '/([eaoiuEAOIU])([eaoiuEAOIU][hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ])/u';
	$VCVV = '/([eaoiuEAOIU][hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ])([eaoiuEAOIU]{2})/u';
	$CVCV = '/([hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ][eaoiuEAOIU])([hkstcnrmpfgzdbv123456HKSTCNRMPFGZDBV7890qQ][eaoiuEAOIU])/u';
	$replacement = '$1' . $hyphen . '$2';
	$string = preg_replace($VCCCCV , $replacement,$string);
	$string = preg_replace($VCCCCV , $replacement,$string);
	$string = preg_replace($VCCCV , $replacement,$string);
	$string = preg_replace($VCCCV , $replacement,$string);
	$string = preg_replace($VCCV , $replacement,$string);
	$string = preg_replace($VCCV , $replacement,$string);
	$string = preg_replace($VCVV , $replacement,$string);
	$string = preg_replace($VCVV , $replacement,$string);
	$string = preg_replace($VVV , $replacement,$string);
	$string = preg_replace($VVV , $replacement,$string);
	$string = preg_replace($VVC , $replacement,$string);
	$string = preg_replace($VVC , $replacement,$string);
	$string = preg_replace($CVCV , $replacement,$string);
	$string = preg_replace($CVCV , $replacement,$string);//CVCVCV → CV-CVCV → CV-CV-CV 少なくとも2回適用が必要
	
	if (isset($extraSeparators)){
		foreach ($extraSeparators as $singleSeparator){
			$string = str_replace($singleSeparator, $singleSeparator.$hyphen, $string);
		}
	}
	//$string = preg_replace($temporal, $original, $string);//子音を元に戻す。今はうまく機能しない
	return $string;
}