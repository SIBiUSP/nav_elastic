<?php

function getFieldsDC($record){
	if (!empty($record['_source']['name'])) {
		$fields['dc:title'] = htmlspecialchars($record['_source']['name']);
	}

	if (!empty($record['_source']['type'])) {
		$fields['dc:type'] = $record['_source']['type'];
	}

	if (!empty($record['_source']['language'][0])) {
		$fields['dc:language'] = $record['_source']['language'][0];
	}

	if (!empty($record['_source']['doi'])) {
		$fields['dc:identifier'] = $record['_source']['doi'];
	}

	if (!empty($record['_source']['url'])) {
		foreach ($record['_source']['url'] as $urlIdentifier) {
			$query = parse_url($urlIdentifier, PHP_URL_QUERY);
			$fields['dc:identifier'] = str_replace($query, rawurlencode($query), $urlIdentifier);
		}
	}

	if (!empty($record['_source']['files']['database'])) {
		foreach ($record['_source']['files']['database'] as $bitstream) {
			$fields['dc:relation'] = 'https:' . $url_base . '/directbitstream/' . $bitstream['bitstream_id'] . '/';
		}
	}

	if (!empty($record['_source']["USP"]["unpaywall"]["best_oa_location"]["url_for_pdf"])) {
		$fields['dc:relation'] = $record['_source']["USP"]["unpaywall"]["best_oa_location"]["url_for_pdf"];
	}

	if (!empty($record['_source']['author'])) {
		foreach ($record['_source']['author'] as $k => $authors) {
			$fields['dc:creator'] = $authors["person"]["name"];
		}
	}

	if (!empty($record['_source']['about'])) {
		foreach ($record['_source']['about'] as $k => $subject) {
			$fields['dc:subject'] = $subject;
		}
	}

	if (!empty($record['_source']['datePublished'])) {
		$fields['dc:date'] = $record['_source']['datePublished'];
	}

	return $fields;
}
?>
