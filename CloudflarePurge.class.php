<?php
	use MediaWiki\MediaWikiServices;

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

			self::purgeUrls( $urls );
			return true;
		}

		public static function onPageContentSaveComplete( $wikiPage, $user, $mainContent, $summaryText, $isMinor, $isWatch, $section, &$flags, $revision, $status, $originalRevId, $undidRevId){
			throw new exception ("ree");	
		}
		
		public static function onNewRevisionFromEditComplete( $wikiPage, $rev, $baseID, $user ) {
			$urls = array();
			$urls[] = $wikiPage->getTitle()->getFullURL();

			if(get_class($wikiPage) === "WikiFilePage"){	
				$urls[] = $wikiPage->getFile()->getURL();
				$thumbPath = $wikiPage->getFile()->getThumbUrl();
				$thumbs = $wikiPage->getFile()->getThumbnails();
				unset($thumbs[0]);
				foreach($thumbs as $u){
					$urls[] = "$thumbPath/$u";
				}
			}
			self::purgeUrls( $urls );

			return true;
		}

		public static function onArticleUpdateBeforeRedirect($article, &$sectionanchor, &$extraq){
			return true;
		}
		
		public static function onArticleViewRedirect( $article ){
			throw new exception("reeeeeeeeeeeee");
		}

		private function purgeUrls( $urls ){
			$config = MediaWikiServices::getInstance()->getMainConfig();
			$wgCloudflareZoneID = $config->get('CloudflareZoneID');
			$wgCloudflareApiToken = $config->get('CloudflareApiToken');
			$wgCloudflareAccountID = $config->get('CloudflareAccountID');

			$str = implode("\", \"", $urls);
			$str = "{\"files\":[\"$str\"]}";
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, "https://api.cloudflare.com/client/v4/zones/$wgCloudflareZoneID/purge_cache");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $str);

			$headers = array();
			$headers[] = "X-Auth-Key: $wgCloudflareAccountID";
			$headers[] = "Authorization: Bearer $wgCloudflareApiToken";
			$headers[] = "Content-Type: application/json";

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($ch);
			if(curl_errno($ch)){
				echo 'Error: ' . curl_error($ch);
			}

			curl_close($ch);
		}
	}
