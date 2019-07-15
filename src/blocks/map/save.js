/**
 * External dependencies
 */
import classnames from 'classnames';
import { noop } from 'lodash';

import Map from '../../components/Map';

import './style.scss';

/**
 * WordPress dependencies
 */
const {
	InnerBlocks
} = wp.editor;

const { Component } = wp.element;

class MapSave extends Component {
	render() {
		const { attributes, className, ...props } = this.props;
		const {
			id,
			content
		} = attributes;

		return (
			<Map
				id={id}
				content={content.replace(/\n/, '<br/>')}
				className={className}
			/>
		);
	}
}

export default MapSave;
