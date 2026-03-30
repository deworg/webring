import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';
import { store as editorStore } from '@wordpress/editor';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEffect } from "@wordpress/element";
import { useEntityProp } from '@wordpress/core-data';

const WebringWebsiteDataMetaPanel = () => {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	if (postType !== 'webring_website') {
		return null;
	}

	const { toggleEditorPanelOpened } = useDispatch( editorStore );

	useEffect( () => {
		toggleEditorPanelOpened( 'webring-website-data-meta/webring-website-data-meta-panel' );
	}, [] );

	return (
		<PluginDocumentSettingPanel
			name="webring-website-data-meta-panel"
			title={ __( 'Website data', 'webring' ) }
			className="webring-website-data-meta-panel"
		>
			<TextControl
				label={ __( 'Website URL', 'webring' ) }
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
