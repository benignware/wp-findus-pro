import './editor.scss';
import './style.scss';

/**
 * External dependencies
 */
import classnames from 'classnames';
import { get } from 'lodash';
import humanizeString from 'humanize-string';
import { camelizeKeys } from 'humps';
import Map from '../../components/Map';

import { getUniqueId } from '../../utils';

/**
 * WordPress dependencies
 */
const { __, _x } = wp.i18n;
const {
	InnerBlocks,
	InspectorControls,
	URLInput,
	URLInputButton
} = wp.editor;

const { Component, Fragment } = wp.element;

const {
	PanelBody,
	TextareaControl,
	ToggleControl,
	RangeControl
} = wp.components;

const { withSelect } = wp.data;

/**
 * Constants
 */

class MapEdit extends Component {
	componentDidMount() {
		const {
			attributes,
			setAttributes,
		} = this.props;

		const {
			id
		} = attributes;

		if (!id) {
			setAttributes({
				...attributes,
				id: getUniqueId('map')
			});
		}
	}

	render() {
		const {
			attributes,
			className,
			isSelected,
			setAttributes,
		} = this.props;

		const {
			id,
			content
		} = attributes;

		return (
			<Fragment>
				<InspectorControls>
					<PanelBody title={ __( 'Map Settings' ) }>
						<TextareaControl
							label={ __('Content') }
							value={ content }
							className="findus-location-control"
							/* eslint-disable jsx-a11y/no-autofocus */
							// Disable Reason: The rule is meant to prevent enabling auto-focus, not disabling it.
							autoFocus={ false }
							/* eslint-enable jsx-a11y/no-autofocus */
							onChange={ ( value ) => setAttributes( { content: value } ) }
						/>

						<RangeControl
							label={ __( 'Zoom' ) }
							value={ attributes.zoom }
							onChange={(value) => setAttributes({
								...attributes,
								zoom: value
							})}
							min={ 0 }
							max={ 24 }
						/>
						<ToggleControl
							label={ __( 'Draggable' ) }
							checked={ !! attributes.draggable }
							onChange={ (value) => {
								setAttributes({
									...attributes,
									draggable: value
								});
							} }
							help={ () => attributes.draggable ? __('Map is draggable') : __(`Map isn't draggable`) }
						/>
						<ToggleControl
							label={ __( 'Disable Default UI' ) }
							checked={ !! attributes.disableDefaultUI }
							onChange={ (value) => {
								setAttributes({
									...attributes,
									disableDefaultUI: value
								});
							} }
							help={ () => attributes.disableDefaultUI ? __('Default UI is disabled') : __(`Default UI is enabled`) }
						/>
						<ToggleControl
							label={ __( 'Zoom Control' ) }
							checked={ !! attributes.zoomControl }
							onChange={ (value) => {
								setAttributes({
									...attributes,
									zoomControl: value
								});
							} }
							help={ () => attributes.zoomControl ? __('Zoom Control is enabled') : __(`Zoom Control isn't enabled`) }
						/>
					</PanelBody>
				</InspectorControls>
				<Map
					id={id}
					content={content.replace(/\n/, '<br/>')}
					className={className}
					{...attributes}
				/>
			</Fragment>
		);
	}
}

export default MapEdit;
