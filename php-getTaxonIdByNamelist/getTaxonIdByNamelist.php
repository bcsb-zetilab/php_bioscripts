<?php

	//<em>Taxonomy ID: </em>215227<br>
	
	if(!file_exists($argv[1])){
		echo <<<EOT
[ERROR] Taxon Names File is not accessible.

EOT;
		exit();
	}
	
	$outfile = "output-" . time() . ".txt";
	file_put_contents($outfile, "");
	
	$taxonlist = file_get_contents($argv[1]);
	$taxonlines = explode("\n", $taxonlist);
	
	//$url = "http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?name=";
	foreach($taxonlines AS $taxonline){
	
		if($taxonline == "") continue;
		
		$taxonline = trim($taxonline);
		
		echo <<<EOT
[INFO] Finding taxon id for {$taxonline}.

EOT;
	
		$url = "http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?name=" . urlencode(trim($taxonline));

		$taxonCurl = httpGet($url);
		
		$taxonid = "";
		if(preg_match("/\<em\>Taxonomy ID\: \<\/em\>(.*)\<br\>/iSU", $taxonCurl, $matches)){
			$taxonid = $matches[1];
			$contents = trim($taxonline) . "\t" . $taxonid . "\n";
			file_put_contents($outfile, $contents, FILE_APPEND);
			echo <<<EOT
[INFO] Get taxon id for {$taxonline} as {$taxonid}.

EOT;
		} else if(preg_match("/<A TITLE=\"species\" HREF=\"\/Taxonomy\/Browser\/wwwtax\.cgi\?mode\=Info\&id\=(.*)\&lvl\=3\&lin\=f\&keep\=1\&srchmode\=1\&unlock\"\>\<BIG\>\<STRONG\>.*\<\/STRONG\>\<\/BIG\>\<\/A\>/iSU", $taxonCurl, $matches)){
			//<A TITLE="species" HREF="/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=71251&lvl=3&lin=f&keep=1&srchmode=1&unlock"><BIG><STRONG>Chelidonium majus</STRONG></BIG></A>
			$taxonid = $matches[1];
			
			$contents = trim($taxonline) . "\t" . $taxonid . "\n";
			file_put_contents($outfile, $contents, FILE_APPEND);
			echo <<<EOT
[INFO] Get taxon id for {$taxonline} as {$taxonid}.

EOT;
		
		} else {
			$contents = trim($taxonline) . "\t" . $taxonid . "\n";
			file_put_contents($outfile, $contents, FILE_APPEND);
			echo <<<EOT
[INFO] Cannot get taxon id for {$taxonline}.

EOT;
		}
				
	}	
	
echo <<<EOT
[INFO] Completed.

EOT;
	
	function httpGet($url)
	{
		$ch = curl_init();  
	 
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HEADER, false); 
	 
		$output=curl_exec($ch);
	 
		curl_close($ch);
		return $output;
	}	
	
?>

