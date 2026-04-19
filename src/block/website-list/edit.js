/**
 * WordPress dependencies
 */
import {
	BlockControls,
	InspectorControls,
	__experimentalImageSizeControl as ImageSizeControl,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	QueryControls,
	RangeControl,
	ToggleControl,
	ToolbarGroup,
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOptionIcon as ToggleGroupControlOptionIcon,
	__experimentalToolsPanel as ToolsPanel,
	__experimentalToolsPanelItem as ToolsPanelItem,
} from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import {
	alignNone,
	grid,
	list,
	positionCenter,
	positionLeft,
	positionRight,
} from '@wordpress/icons';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Module Constants
 */
const CATEGORIES_LIST_QUERY = {
	per_page: -1,
	_fields: 'id,name',
	context: 'view',
};

const imageAlignmentOptions = [
	{
		value: 'none',
		icon: alignNone,
		label: __( 'None' ),
	},
	{
		value: 'left',
		icon: positionLeft,
		label: __( 'Left' ),
	},
	{
		value: 'center',
		icon: positionCenter,
		label: __( 'Center' ),
	},
	{
		value: 'right',
		icon: positionRight,
		label: __( 'Right' ),
	},
];

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __, _x } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * Component for block controls.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update block attributes.
 *
 * @return {Element} Controls component.
 */
function Controls( { attributes, setAttributes } ) {
	const {
		categories,
		selectedAuthor,
		displayFeaturedImage,
		displayWebsiteUrl,
		postLayout,
		columns,
		featuredImageAlign,
		featuredImageSizeSlug,
		featuredImageSizeWidth,
		featuredImageSizeHeight,
		addLinkToFeaturedImage,
	} = attributes;
	const {
		imageSizes,
		defaultImageWidth,
		defaultImageHeight,
		categoriesList,
	} = useSelect(
		( select ) => {
			const { getEntityRecords } = select( coreStore );
			const settings = select( blockEditorStore ).getSettings();

			return {
				defaultImageWidth:
					settings.imageDimensions?.[ featuredImageSizeSlug ]
						?.width ?? 0,
				defaultImageHeight:
					settings.imageDimensions?.[ featuredImageSizeSlug ]
						?.height ?? 0,
				imageSizes: settings.imageSizes,
				categoriesList: getEntityRecords(
					'taxonomy',
					'webring_category',
					CATEGORIES_LIST_QUERY
				),
			};
		},
		[ featuredImageSizeSlug ]
	);

	const imageSizeOptions = imageSizes
		.filter( ( { slug } ) => slug !== 'full' )
		.map( ( { name, slug } ) => ( {
			value: slug,
			label: name,
		} ) );
	const categorySuggestions =
		categoriesList?.reduce(
			( accumulator, category ) => ( {
				...accumulator,
				[ category.name ]: category,
			} ),
			{}
		) ?? {};
	const selectCategories = ( tokens ) => {
		const hasNoSuggestion = tokens.some(
			( token ) =>
				typeof token === 'string' && !categorySuggestions[ token ]
		);
		if (hasNoSuggestion) {
			return;
		}
		// Categories that are already will be objects, while new additions will be strings (the name).
		// allCategories normalize the array so that they are all objects.
		const allCategories = tokens.map( ( token ) => {
			return typeof token === 'string'
				? categorySuggestions[ token ]
				: token;
		} );
		// We do nothing if the category is not selected from suggestions.
		if (allCategories.includes( null )) {
			return false;
		}
		setAttributes( { categories: allCategories } );
	};

	return (
		<>
			<ToolsPanel
				label={ __( 'Post meta', 'webring-manager' ) }
				resetAll={ () =>
					setAttributes( {
						displayWebsiteUrl: false,
					} )
				}
			>
				<ToolsPanelItem
					hasValue={ () => !!displayWebsiteUrl }
					label={ __( 'Display website URL', 'webring-manager' ) }
					onDeselect={ () =>
						setAttributes( { displayWebsiteUrl: false } )
					}
					isShownByDefault
				>
					<ToggleControl
						label={ __( 'Display website URL', 'webring-manager' ) }
						checked={ displayWebsiteUrl }
						onChange={ ( value ) =>
							setAttributes( { displayWebsiteUrl: value } )
						}
					/>
				</ToolsPanelItem>
			</ToolsPanel>
			<ToolsPanel
				label={ __( 'Featured image' ) }
				resetAll={ () =>
					setAttributes( {
						displayFeaturedImage: false,
						featuredImageAlign: undefined,
						featuredImageSizeSlug: 'thumbnail',
						featuredImageSizeWidth: null,
						featuredImageSizeHeight: null,
						addLinkToFeaturedImage: false,
					} )
				}
			>
				<ToolsPanelItem
					hasValue={ () => !!displayFeaturedImage }
					label={ __( 'Display featured image' ) }
					onDeselect={ () =>
						setAttributes( { displayFeaturedImage: false } )
					}
					isShownByDefault
				>
					<ToggleControl
						label={ __( 'Display featured image' ) }
						checked={ displayFeaturedImage }
						onChange={ ( value ) =>
							setAttributes( { displayFeaturedImage: value } )
						}
					/>
				</ToolsPanelItem>
				{ displayFeaturedImage && (
					<>
						<ToolsPanelItem
							hasValue={ () =>
								featuredImageSizeSlug !== 'thumbnail' ||
								featuredImageSizeWidth !== null ||
								featuredImageSizeHeight !== null
							}
							label={ __( 'Image size' ) }
							onDeselect={ () =>
								setAttributes( {
									featuredImageSizeSlug: 'thumbnail',
									featuredImageSizeWidth: null,
									featuredImageSizeHeight: null,
								} )
							}
							isShownByDefault
						>
							<ImageSizeControl
								onChange={ ( value ) => {
									const newAttrs = {};
									if (value.hasOwnProperty( 'width' )) {
										newAttrs.featuredImageSizeWidth =
											value.width;
									}
									if (value.hasOwnProperty( 'height' )) {
										newAttrs.featuredImageSizeHeight =
											value.height;
									}
									setAttributes( newAttrs );
								} }
								slug={ featuredImageSizeSlug }
								width={ featuredImageSizeWidth }
								height={ featuredImageSizeHeight }
								imageWidth={ defaultImageWidth }
								imageHeight={ defaultImageHeight }
								imageSizeOptions={ imageSizeOptions }
								imageSizeHelp={ __(
									'Select the size of the source image.'
								) }
								onChangeImage={ ( value ) =>
									setAttributes( {
										featuredImageSizeSlug: value,
										featuredImageSizeWidth: undefined,
										featuredImageSizeHeight: undefined,
									} )
								}
							/>
						</ToolsPanelItem>
						<ToolsPanelItem
							hasValue={ () => !!featuredImageAlign }
							label={ __( 'Image alignment' ) }
							onDeselect={ () =>
								setAttributes( {
									featuredImageAlign: undefined,
								} )
							}
							isShownByDefault
						>
							<ToggleGroupControl
								className="editor-latest-posts-image-alignment-control"
								__next40pxDefaultSize
								label={ __( 'Image alignment' ) }
								value={ featuredImageAlign || 'none' }
								onChange={ ( value ) =>
									setAttributes( {
										featuredImageAlign:
											value !== 'none'
												? value
												: undefined,
									} )
								}
							>
								{ imageAlignmentOptions.map(
									( { value, icon, label } ) => {
										return (
											<ToggleGroupControlOptionIcon
												key={ value }
												value={ value }
												icon={ icon }
												label={ label }
											/>
										);
									}
								) }
							</ToggleGroupControl>
						</ToolsPanelItem>
						<ToolsPanelItem
							hasValue={ () => !!addLinkToFeaturedImage }
							label={ __( 'Add link to featured image' ) }
							onDeselect={ () =>
								setAttributes( {
									addLinkToFeaturedImage: false,
								} )
							}
							isShownByDefault
						>
							<ToggleControl
								label={ __( 'Add link to featured image' ) }
								checked={ addLinkToFeaturedImage }
								onChange={ ( value ) =>
									setAttributes( {
										addLinkToFeaturedImage: value,
									} )
								}
							/>
						</ToolsPanelItem>
					</>
				) }
			</ToolsPanel>
			<ToolsPanel
				label={ __( 'Sorting and filtering' ) }
				resetAll={ () =>
					setAttributes( {
						categories: undefined,
						selectedAuthor: undefined,
						columns: 3,
					} )
				}
			>
				<ToolsPanelItem
					hasValue={ () =>
						categories?.length > 0 ||
						!!selectedAuthor
					}
					label={ __( 'Sort and filter' ) }
					onDeselect={ () =>
						setAttributes( {
							categories: undefined,
							selectedAuthor: undefined,
						} )
					}
					isShownByDefault
				>
					<QueryControls
						categorySuggestions={ categorySuggestions }
						onCategoryChange={ selectCategories }
						selectedCategories={ categories }
					/>
				</ToolsPanelItem>

				{ postLayout === 'grid' && (
					<ToolsPanelItem
						hasValue={ () => columns !== 3 }
						label={ __( 'Columns' ) }
						onDeselect={ () =>
							setAttributes( {
								columns: 3,
							} )
						}
						isShownByDefault
					>
						<RangeControl
							__next40pxDefaultSize
							label={ __( 'Columns' ) }
							value={ columns }
							onChange={ ( value ) =>
								setAttributes( { columns: value } )
							}
							min={ 2 }
							max={ 6 }
							required
						/>
					</ToolsPanelItem>
				) }
			</ToolsPanel>
		</>
	)
}

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to update block attributes.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const {
		postLayout,
	} = attributes;

	const inspectorControls = (
		<InspectorControls>
			<Controls
				attributes={ attributes }
				setAttributes={ setAttributes }
			/>
		</InspectorControls>
	);

	const layoutControls = [
		{
			icon: list,
			title: _x( 'List view', 'Latest posts block display setting' ),
			onClick: () => setAttributes( { postLayout: 'list' } ),
			isActive: postLayout === 'list',
		},
		{
			icon: grid,
			title: _x( 'Grid view', 'Latest posts block display setting' ),
			onClick: () => setAttributes( { postLayout: 'grid' } ),
			isActive: postLayout === 'grid',
		},
	];

	return (
		<>
			{ inspectorControls }
			<BlockControls>
				<ToolbarGroup controls={ layoutControls }/>
			</BlockControls>

			<div { ...useBlockProps() } title="wrapper">
				<ServerSideRender
					block="webring-manager/website-list"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
