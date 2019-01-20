<?php
/**
 * @package Ai_Kotoba
 * @version 0.0.39
 */
/*
Plugin Name: Ai Kotoba
Plugin URI: https://www.futaba.love/plugins/ai-kotoba/
Description: This is JUST a plugin. When activated you will randomly see a lyric from the LYRICS in the upper right of your admin screen on every page.
Author: Futaba Sakura
Version: 0.0.39
Author URI: https://profiles.wordpress.org/imouto/
License: GPLv2 or later
Tags: hello dolly, admin, random, lyrics, ai kotoba
*/

class AiKotoba {
	const PLUGIN_OPTION_NAME = "AiKotobaOption"; // 不要再修改
	const ITEM_LYRICS = "Lyrics"; // 不要再修改
	const LYRICS_DESCRIPTION = "Feel free to input your favourite LYRICS or someting else. When activated you will randomly see a lyric from the LYRICS in the upper right of your admin screen on every page";
	static $_option = null;

	static function settings_api_init() {
		self::$_option = get_option(self::PLUGIN_OPTION_NAME);

		add_option(self::PLUGIN_OPTION_NAME, [
			self::ITEM_LYRICS => '', // lyrics
		]);

		$optionGroup = 'general';
		register_setting($optionGroup, self::PLUGIN_OPTION_NAME);

		$page = 'general';
		add_settings_field('aikotobaLyrics', 'Ai Kotoba', 'AiKotoba::lyrics_callback', $page);
	}

	static function lyrics_callback() {
		if (!isset(self::$_option[self::ITEM_LYRICS])) {
			self::$_option[self::ITEM_LYRICS] = "";
		}
		$textAreaName = sprintf("%s[%s]", self::PLUGIN_OPTION_NAME, self::ITEM_LYRICS);
		echo "<textarea class=\"large-text code\" name=\"$textAreaName\"  type=\"textarea\" cols=\"40\" rows=\"5\">". esc_textarea(self::$_option[self::ITEM_LYRICS]) . "</textarea><p class=\"description\">" . self::LYRICS_DESCRIPTION . "</p>";
		// echo $text;
	}

	static function ai_kotoba_get_lyric() {
		/** These are the lyrics to Ai Kotoba */
		if (!isset(self::$_option[self::ITEM_LYRICS])) {
			self::$_option[self::ITEM_LYRICS] = "";
		}
		$lyrics = self::$_option[self::ITEM_LYRICS];
	
		// Here we split it into lines
		$lyrics = explode( "\n,", $lyrics );
	
		// And then randomly choose a line
		return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
	}

	// This just echoes the chosen line, we'll position it later
	static function ai_kotoba() {
		$chosen = self::ai_kotoba_get_lyric();
		echo "<p id='kotoba'>$chosen</p>";
	}

	// We need some CSS to position the paragraph
	static function kotoba_css() {
		// This makes sure that the positioning is also good for right-to-left languages
		$x = is_rtl() ? 'left' : 'right';

		echo "
		<style type='text/css'>
		#kotoba {
			float: $x;
			padding-$x: 15px;
			padding-top: 5px;		
			margin: 0;
			font-size: 11px;
		}
		.block-editor-page #kotoba {
			display: none;
		}
		</style>
		";
	}
}

add_action('admin_init', 'AiKotoba::settings_api_init');

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'AiKotoba::ai_kotoba' );

add_action( 'admin_head', 'AiKotoba::kotoba_css' );

?>