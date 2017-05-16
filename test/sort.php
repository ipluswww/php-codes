<?php
function displayNamecompare($a, $b){
        if ($a['displayName'] == $b['displayName']) {
            return 0;
        }
        
        return ($a['displayName'] < $b['displayName']) ? -1 : 1;
    }


        $response['list'] = array(array('displayName' => 'a', 'value' => '1'),
		array('displayName' => 'a', 'value' => '2'),
		array('displayName' => 'b', 'value' => '1'),
		array('displayName' => 'f', 'value' => '5'),
		array('displayName' => 'c', 'value' => '1'),
		);

        usort($response['list'], 'displayNamecompare');
		$response['list'] = array_slice($response['list'], 1, 2);
	var_dump($response['list']);

?>