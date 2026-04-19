import { TextControl } from '@wordpress/components';
import { useEntityProp } from '@wordpress/core-data';
import { useDispatch, useSelect } from '@wordpress/data';
import { PluginDocumentSettingPanel, store as editorStore } from '@wordpress/editor';
import { useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

const WebringWebsiteDataMetaPanel = () => {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const { toggleEditorPanelOpened } = useDispatch( editorStore );

	useEffect( () => {
			toggleEditorPanelOpened( 'webring-website-data-meta/webring-website-data-meta-panel' );
	}, [ toggleEditorPanelOpened ] );

	if (postType !== 'webring_website') {
		return null;
	}

	return (
		<PluginDocumentSettingPanel
			name="webring-website-data-meta-panel"
			title={ __( 'Website data', 'webring-manager' ) }
			className="webring-website-data-meta-panel"
		>
			<TextControl
				label={ __( 'Website URL', 'webring-manager' ) }
				value={ meta?.webring_website_url || '' }
				onChange={ ( value ) => setMeta( { ...meta, webring_website_url: value } ) }
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'webring-website-data-meta', {
	render: WebringWebsiteDataMetaPanel,
	icon: 'admin-generic',
} );
