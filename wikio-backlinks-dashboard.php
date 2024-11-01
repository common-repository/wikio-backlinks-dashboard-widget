<?php
/*
	Plugin Name: Wikio Backlinks Dashboard
	Plugin URI: http://wikio.com
	Description: The Backlinks Widget Dashboard allows you to display in your dashboard articles on other blogs that have cited you! Simply activate and go to your <a href="index.php">dashboard</a> 
	Version: 0.1.2
	Author: Wikio
	Author URI: http://wikio.com
	
	Copyright 2009  Wikio  (email : widgets@wikio.com)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

	$widget_options = get_option( 'dashboard_widget_options' );
	if ( !$widget_options || !is_array($widget_options) )
		$widget_options = array();
		
	// ajoute les options	
	$widget_options['wikio_dashboard_getlinks'] = array(
				
				'url' => apply_filters( 'wikio_dashboard_getlinks_link', 'http://api.wikio.fr/source/V1/getLinks?url=' . trailingslashit( get_option('home') ) ),
				'dir' => apply_filters( 'wikio_dashboard_getlinks_dir', 'in'),
				'items' => 10
			);
	
	update_option( 'dashboard_widget_options', $widget_options );
	
	
	// Create the function to output the contents of our Dashboard Widget
	function wikio_dashboard_getlinks_function() {
		
		$widgets = get_option( 'dashboard_widget_options' );
		
		@extract( @$widgets['wikio_dashboard_getlinks'], EXTR_SKIP );
		
		
		$wp_version = get_bloginfo( 'version' );
		$wp_version = explode(".", $wp_version);
		$wp_version = implode("", $wp_version);
		
		if ($wp_version < 200) { $wp_version = $wp_version * 10;}
		if ($wp_version > 283){
			
			// Simplepie compliance	
			
			$feed = $url.'&dir='.$dir;
			$rss = fetch_feed( $feed );
			
			if ( is_wp_error($rss) ) {
				if ( is_admin() || current_user_can('manage_options') ) {
					echo '<p>';
					print('Please, submit your site here : <a href="http://www.wikio.com/addsource">Wikio US</a> / <a href="http://www.wikio.co.uk/addsource">Wikio UK</a> / <a href="http://www.wikio.it/addsource">Wikio IT</a> / <a href="http://www.wikio.es/addsource">Wikio ES</a> / <a href="http://www.wikio.de/addsource">Wikio DE</a> / <a href="http://www.wikio.fr/addsource">Wikio FR</a>');
					echo '</p>';
				}
				return;
			}
			
			if ($rss->get_items()){
				foreach ($rss->get_items(0, 10) as $item): ?>
					<p>
					<strong><a href="<?php echo $item->get_permalink(); ?>" target="_blank"><?php echo $item->get_title(); ?></a></strong><br />
					<?php echo $item->get_description(); ?>
					<p><small><?php echo $item->get_date('j F Y | g:i a'); ?></small></p>
					</p>
				<?php endforeach;
			} else {
				echo "No incoming links.";
			}
		}

		else { ?>
			<p>
				<strong>Please, update your Wordpress (your version is <?php echo get_bloginfo( 'version' ); ?>) then, reload this page!</strong>
			</p>
			<?php
		}
	} 
	
	// Create the function use in the action hook
	
	function wikio_dashboard_getlinks() {
		$widget_title = 'Wikio - '.__( 'Incoming Links' );
		wp_add_dashboard_widget('wikio_dashboard_getlinks', $widget_title, 'wikio_dashboard_getlinks_function');	
	} 
	
	
	
	// Hoook into the 'wp_dashboard_setup' action to register our other functions
	
	add_action('wp_dashboard_setup', 'wikio_dashboard_getlinks' );

?>