<?php
require_once 'simple_html_dom.php';
$url = "https://yandex.ru/search/?lr=20016&msid=1508325721.1612.22876.23228&text=%D0%B0%D0%B0%D0%B0";

function translate($text) {
	$api_key = "trnsl.1.1.20171018T120429Z.f4d8a10bddc90d58.7f0c34c244c682c6c5ec2ac3d9a90ac65ed699ab";
	$api_url = "https://translate.yandex.net/api/v1.5/tr.json/translate?lang=ru-en&key=$api_key&format=html";
	$ch = curl_init($api_url);  
    //curl_setopt($ch, CURLOPT_HEADER, 0);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "text=".urlencode($text));
    $out = curl_exec($ch);      
    curl_close($ch);		
	return json_decode($out);
}

$cont = file_get_contents($url);
$html = new simple_html_dom(); 
if ($html->load($cont)) {			
	// Берем контент	
	$a = $html->find('.main__content',0)->innertext;
	
	/*$w = translate($a);	
	var_dump($w);die();
	if ($w->code==200) {
		var_dump($w->text[0]);die();
		$html->find('.main',0)->innertext = $w->text[0];
	}*/
	foreach ($html->find('.main__content ul[role=main]>li') AS $item) {
		if (mb_strlen($item)>10000) {
			//echo $item->find("li>h2",0)->innertext;
			//echo $item; 
			foreach ($item->find(".scroller__item") AS $subitem) {
				echo mb_strlen($subitem->innertext)."<br>";
			}
			
		//$item->find('.organic__subtitle',0)->innertext = "123";
		} else {
			//$html->find('.main__content ul[role=main]>li')
			echo mb_strlen($item->innertext);
			$w = translate($item->innertext);	
			var_dump($w);die();
			if ($w->code==200) {
			//var_dump($w->text[0]);die();
					$item->innertext = $w->text[0];
			}			
		}
	}
}	

/*organic__subtitle typo typo_type_greenurl - подзаголовок
class="organic__content-wrapper clearfix" - текст
*/
echo $html;
//echo $top;
//echo $date;

$html->clear();
unset($html);
?>