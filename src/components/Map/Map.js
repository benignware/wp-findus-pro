import React, { Component, createRef } from 'react';
import omit from 'lodash.omit';
// import findus from '../../../node_modules/findus/dist/findus.js';
// import findus from '../../../node_modules/findus/src/js/lib/Findus.js';
// import '../../lib/findus/findus.js';
// import findus from 'findus';

const findus = global.findus;

export default class Map extends Component {
  constructor() {
    super(...arguments);

    this.ref = createRef();
    this.findus = null;
  }

  getOptions() {
    let options = omit(this.props, [
      'id',
      'className',
      'children'
    ]);

    // options = {
    //   zoom: 8,
    //   zoomControl: true,
    //   scaleControl: true,
    //   ...options
    // };

    console.log('options', options);

    return options;
  }

  componentDidMount() {
    const { current: element } = this.ref;
    const options = this.getOptions();

    this.findus = global.findus(element, options);
  }

  componentWillUnmount() {
    this.findus.destroy();
  }

  componentDidUpdate() {
    // this.findus.setOptions(this.getOptions());

    console.log('findus..', this.getOptions());
    this.findus.update(this.getOptions());
  }

  render() {
    const { className, children } = this.props;

    return (
      <div ref={this.ref} className={className}/>
    );
  }
}
