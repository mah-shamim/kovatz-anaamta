<?php
/**
 * Created by PhpStorm.
 * User: malayladu
 * Date: 2019-01-11
 * Time: 14:57
 */

$system_info = new Wtlwp_Sytem_Info();

?>
<div title="Copy">
	<button class="wtlwp-click-to-copy-btn bg-gray-200 cursor-pointer border border-gray-100 text-sm leading-5 font-medium rounded-t-md text-gray-700 bg-white transition duration-150 ease-in-out px-3 py-1 hover:bg-gray-100 hover:border-2 hover:border-gray-200 hover:text-gray-700 focus:border-2 focus:border-gray-200" data-clipboard-action="copy" data-clipboard-target="#tlwp-system-info-data"><svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title><?php esc_html_e('Copy', 'temporary-login-without-password' ); ?></title><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg></button>
</div>
<div class="wrap wtlwp-form bg-white rounded-md shadow-md -mt-0.5" id="tlwp-system-info-data">
	<?php
		echo $system_info->render_system_info_page();
	?>
</div>

<br />
