<?php
	class CloudflarePurge{
		public static function onSpecialUploadComplete( &$upload ){
			$urls = array();
			$urls[] = $upload->mLocalFile->getURL();
			$urls[] = $upload->mLocalFile->getTitle()->getFullURL();
			$thumbPath = $upload->mLocalFile->getThumbUrl();
			$thumbs = $upload->mLocalFile->getThumbnails();
			unset($thumbs[0]);
			foreach($thumbs as $u){
				$urls[] = "$thumbPath/$u";
			}	

			purgeUrls( $urls );
		}

		private function purgeUrls( $urls ){	
			$str = implode("\", \"", $urls);
			$str = "{\"files\":[\"$str\"]}";


			$ch = curl_init();
			throw new exception($str);	
		}
	}
