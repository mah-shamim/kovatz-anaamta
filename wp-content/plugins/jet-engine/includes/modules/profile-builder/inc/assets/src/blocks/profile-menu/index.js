import Edit from './edit';

const { __ } = wp.i18n;

const { registerBlockType } = wp.blocks;

const {
	Path,
	SVG
} = wp.components;

const Icon = <SVG width="38" height="24" viewBox="0 0 64 40" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 16C7.44772 16 7 16.4477 7 17C7 17.5523 7.44771 18 8 18H56C56.5523 18 57 17.5523 57 17C57 16.4477 56.5523 16 56 16H8Z" fill="currentColor"/><path d="M8 20C7.44772 20 7 20.4477 7 21C7 21.5523 7.44771 22 8 22H56C56.5523 22 57 21.5523 57 21C57 20.4477 56.5523 20 56 20H8Z" fill="currentColor"/><path d="M8 24C7.44772 24 7 24.4477 7 25C7 25.5523 7.44772 26 8 26H31C31.5523 26 32 25.5523 32 25C32 24.4477 31.5523 24 31 24H8Z" fill="currentColor"/><path fillRule="evenodd" clipRule="evenodd" d="M50 4V8H60C62.2091 8 64 9.79086 64 12V36C64 38.2091 62.2091 40 60 40H4C1.79086 40 0 38.2091 0 36V4C0 1.79086 1.79086 0 4 0H46C48.2091 0 50 1.79086 50 4ZM2 8H16V2H4C2.89543 2 2 2.89543 2 4V8ZM18 8V2H32V8H18ZM34 8H48V4C48 2.89543 47.1046 2 46 2H34V8ZM2 10V36C2 37.1046 2.89543 38 4 38H60C61.1046 38 62 37.1046 62 36V12C62 10.8954 61.1046 10 60 10H2Z" fill="currentColor"/></SVG>;

registerBlockType( 'jet-engine/profile-menu', {
	icon: Icon,
	title: __( 'Profile Menu' ),
	category: 'jet-engine',
	attributes: {
		menu_context: {
			type: 'string',
			default: 'account_page',
		},
		account_roles: {
			default: [],
		},
		user_roles: {
			default: [],
		},
		add_main_slug: {
			type: 'boolean',
			default: false,
		},
		menu_layout: {
			type: 'string',
			default: 'horizontal',
		},
		menu_layout_tablet: {
			type: 'string',
			default: 'horizontal',
		},
		menu_layout_mobile: {
			type: 'string',
			default: 'horizontal',
		},
	},
	className: 'jet-profile',
	edit: Edit,
	save: ( props ) => {
		return null;
	},
} );
