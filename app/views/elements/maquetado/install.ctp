
<br/>

<?php 

$instalAcrobat = "AdbeRdr1001_es_ES.exe";
$instalJavaVM = "jre-6u24-windows-i586-s.exe";
//$str = explode(" ",php_uname());
//$os = trim($str[0]);
//
//if($os != 'Windows'):
//	$instalAcrobat = "AdobeReader_esp-8.1.7-1.i486.rpm";
//	$instalJavaVM = "jre-6u24-linux-i586.bin";
//endif;

$w = 30;
$opacityMin = "1";

?>
<div style="float: right;">
<a href="<?php echo $this->base?>/<?php echo $instalAcrobat?>" target="_blank" style="text-decoration: none;">
<img src="<?php echo $this->base?>/img/pdf2.png" border="0"
style="margin:5px;opacity:<?php echo $opacityMin?>;filter:alpha(opacity=80)" onmouseover="this.style.opacity=1;this.filters.alpha.opacity=100" 
onmouseout="this.style.opacity=<?php echo $opacityMin?>;this.filters.alpha.opacity=80" alt="" />
</a>
<a href="<?php echo $this->base?>/<?php echo $instalJavaVM?>" target="_blank" style="text-decoration: none;">
<img src="<?php echo $this->base?>/img/java2.png" border="0"
style="margin:5px;opacity:<?php echo $opacityMin?>;filter:alpha(opacity=80)" onmouseover="this.style.opacity=1;this.filters.alpha.opacity=100" 
onmouseout="this.style.opacity=<?php echo $opacityMin?>;this.filters.alpha.opacity=80" alt="" />
</a>
</div>