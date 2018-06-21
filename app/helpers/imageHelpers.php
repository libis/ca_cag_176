<?php
	require_once(__CA_LIB_DIR__.'/core/Configuration.php');
	require_once('cURL.php');

	function getImageThumbnailLink($imagePid)
	{
		$thumbnailUrl = getImageThumbnailUrl($imagePid);

		if (strlen($thumbnailUrl) > 0) {
            return '<img src="'.$thumbnailUrl.'" style="max-height: 300px; max-width: 200px">';
		}

		return "";
	}

    # wordt gebruikt om de afbeelding in PDF te tonen.
    # om meerdere urls op te halen
    function getImageThumbnailsBase($imagePids)
    {
        $tempImgs = array();

        if(count($imagePids) > 0) {

            foreach($imagePids as $imagePid) {
                array_push($tempImgs, getImageThumbnailBase($imagePid));
            }
        }

        return $tempImgs;
    }

    # wordt gebruikt om de afbeelding in PDF te tonen.
    # voor 1 url op te halen
    function getImageThumbnailBase($imagePid)
	{
		$thumbnailUrl = getImageThumbnailUrl($imagePid);
		
		if(strlen($thumbnailUrl) > 0) {

			$vo_http_client = new Zend_Http_Client();

			$config = array(
                'adapter'    => 'Zend_Http_Client_Adapter_Proxy',
                'proxy_host' => 'icts-http-gw.cc.kuleuven.be',
                'proxy_port' => 8080,
                'timeout'    => 30
			);

			$vo_http_client->setConfig($config);

			$vo_http_client->setUri($thumbnailUrl);
		
			$vo_http_response = $vo_http_client->request();
			$thumb = $vo_http_response->getBody();
			$try = 0;
			
			while($try < 10)
			{
				if (!$vo_http_response->isError()){
					return '<img src="data:image/jpeg;base64,'.base64_encode($thumb).'">';
					break;
				} else {
                   //retry
                   sleep(1);
                   $vo_http_client->setUri($thumbnailUrl);
                   $vo_http_response = $vo_http_client->request();

                   if (!$vo_http_response->isError()){
                        return '<img src="data:image/jpeg;base64,'.base64_encode($thumb).'">';
                   } else {
                       $try++;
                    }
				}
			}
		}

		return "";
	}


    # wordt gebruikt in de thumbnail view van de zoekresultaten vb ca_objects_results_thumbnail_html.php
    function getImageThumbnailView($imagePid)
	{
		$thumbnailUrl = getImageThumbnailUrl($imagePid);

		if (strlen($thumbnailUrl) > 0) {			
			return '<img src="'.$thumbnailUrl.'" width="172px">';					
		}

		return "";
	}


	function getImageThumbnailUrl($imagePid)
	{
		if (strlen($imagePid) > 0) {

            $imageUrl = "http://resolver.libis.be/".$imagePid."?quality=CRITICAL_ARCHIVE";
			
			return $imageUrl;
		}

		return "";
	}


    function getImageViewMainUrl($imagePid)
    {
        if (strlen($imagePid) > 0) {

            $imageUrl = "http://resolver.libis.be/".$imagePid."/representation";

            return $imageUrl;

        }

        return "";
    }

    # GetImagePids wordt gebruikt om meerdere afbeeldingen uit een afbeelding te halen.
    # Als er maar 1 afbeelding is wordt er een string teruggeven anders een array
    function getImagePids($imageUrls)
    {
	    if (!is_null($imageUrls)) {

            foreach($imageUrls as $attribute) {
                    if (strpos($attribute, '_')) {
                        $imagePids[] = substr($attribute, 0, strpos($attribute, '_'));
                    } else {
                        $imagePids[] = trim($attribute);
                    }
            }

            return $imagePids;

        }

        return "";
    }

?>
