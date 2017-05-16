<?php
require '' . '/review_helper.php';

function start()
{
		$category = 'all';
		
		if (empty($category)) $category = 'all';
		

		$keyword = 'computer';
		
		if (($category == 'all') && (empty($keyword)))
		{
			$this->load->view('index', compact('category', 'keyword'));
			return;
		}
		else {
			
			$curl = array();

			//=============================amazon============================
			$curl[str_amazon] = getAmazonCurl('com', $category, $keyword);
			//=============================amazon============================
			
			
			$mh = curl_multi_init();

			//add the two handles
			foreach($curl as $ch) {
				curl_multi_add_handle($mh,$ch);
			}
			
			$running=null;
			
			//execute the handles
			do {
				curl_multi_exec($mh,$running);
			} while($running > 0);
			
			
			$return = getProductListResult($curl); 
			
			foreach($curl as $ch) {
				curl_multi_remove_handle($mh, $ch);
				curl_close($ch); 
			}
			
			curl_multi_close($mh);
			
			$product_list = $return->product;
			$totalRecords = $return->totalRecords;
			
			var_dump($product_list);
			var_dump($totalRecords);
			return;
		}
}

start();