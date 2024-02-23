<?php
namespace Jet_Engine\Modules\Data_Stores;

class Macros {

	public function __construct() {
		add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );
	}

	public function register_macros() {
		require_once Module::instance()->module_path( 'macros/get-store.php' );
		require_once Module::instance()->module_path( 'macros/get-users-for-store-item.php' );
		require_once Module::instance()->module_path( 'macros/get-store-count.php' );

		new Macros\Get_Store();
		new Macros\Get_Users_For_Store_Item();
		new Macros\Get_Store_Count();
	}

}
