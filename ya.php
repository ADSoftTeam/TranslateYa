<?php
require_once 'simple_html_dom.php';
$text = isset($_GET['text']) ? urlencode($_GET['text']) : "пример";
//echo $text;
$url = "https://yandex.ru/search/?text=$text";

function translate($text) {
	$api_key = "trnsl.1.1.20171018T120429Z.f4d8a10bddc90d58.7f0c34c244c682c6c5ec2ac3d9a90ac65ed699ab";	
	$api_key = "trnsl.1.1.20171018T135335Z.42c5b12eb6f0a1ac.2f98fddb1aa3d463f312399f5ad79a10602707b0";
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
	$arr = array();	
	foreach ($html->find('.main__content ul[role=main]>li') AS $item) {
		$tmp = "";
		if (mb_strlen($item)>=10000) {
			// Похоже это список видео (возможно картинок)
			// В цикле переводим все для каждого элемента списка - заменяя оригинал			
			foreach ($item->find(".scroller__item") AS $subitem) {
				$w = translate($subitem->innertext);
				if ($w->code==200) {			
					$subitem->innertext = $w->text[0];
				}
				$tmp .= $w->text[0];
			}		
		} else {			
			// Кусок меньше 10000, переводим его и заменяем оригинал
			$w = translate($item->innertext);			
			if ($w->code==200) {			
				$item->innertext = $w->text[0];
				$tmp = $w->text[0];
			}			
		}
		$arr[] = $tmp; 
		// готовим массив с переведенными результатами поиска, чтобы потом их "перемешивать"
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