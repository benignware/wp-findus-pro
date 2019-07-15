import React, { Component, createRef } from 'react';
import omit from 'lodash.omit';
import Findus from '../../lib/findus';

export default class Map extends Component {
  constructor() {
    super(...arguments);

    this.ref = createRef();
    this.findus = null;
  }

  getOptions() {
    return omit(this.props, [
      'id',
      'className',
      'children'
    ]);
  }

  componentDidMount() {
    const { current: element } = this.ref;
    const options = this.getOptions();

    this.findus = new Findus(element, options);
  }

  componentWillUnmount() {
    this.findus.destroy();
  }

  componentDidUpdate() {
    this.findus.setOptions(this.getOptions());
    this.findus.update();
  }

  render() {
    const { className, children } = this.props;

    return (
      <div ref={this.ref} className={className}/>
    );
  }
}
