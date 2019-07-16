import { select, event } from 'd3-selection';
import { drag } from 'd3-drag';
import { json } from 'd3-fetch';
import { timer } from 'd3-timer';
import { geoGraticule, geoOrthographic, geoPath } from 'd3-geo';
import * as topojson from 'topojson';

const d3 = {
  select,
  get event() {
    return event;
  },
  drag,
  json,
  timer,
  geoGraticule,
  geoOrthographic,
  geoPath,
};

/**
 * We will need this later
 * @type {DOMParser}
 */
var parser = new DOMParser();

/**
 * Easing Functions - inspired from http://gizma.com/easing/
 * only considering the t value for the range [0, 1] => [0, 1]
 * https://gist.github.com/gre/1650294
 * @param  {Float} power [0, 1]
 * @return {Float} [0, 1]
 */
var easeIn = function(power) {
  return function(t) {
    return Math.pow(t, power);
  };
};
var easeOut = function(power) {
  return function(t) {
    return 1 - Math.abs(Math.pow(t - 1, power));
  };
};
var easeInOut = function(power) {
  return function(t) {
    return t < 0.5
      ? easeIn(power)(t * 2) / 2
      : easeOut(power)(t * 2 - 1) / 2 + 0.5;
  };
};
var Easing = {
  linear: easeInOut(1),
  easeIn: easeIn(2),
  easeOut: easeOut(2),
  easeInOut: easeInOut(2),
  easeInCubic: easeIn(3),
  easeOutCubic: easeOut(3),
  easeInOutCubic: easeInOut(3),
  easeInQuart: easeIn(4),
  easeOutQuart: easeOut(4),
  easeInOutQuart: easeInOut(4),
  easeInQuint: easeIn(5),
  easeOutQuint: easeOut(5),
  easeInOutQuint: easeInOut(5),
};

var Globalicious = function(wrapper, options) {
  var self = this;

  // defaults
  this.options = {
    speed: 1,
    incline: -15,
    startingRotation: 0,
    outline: '#000',
    land: '#222',
    sea: '#fff',
    borders: '#fff',
    gridOver: 'rgba(119, 119, 119, 0.5)',
    gridUnder: false,
    data: '/data/world-110m.json',
    goToDuration: 800,
    goToEasing: 'easeOut',
  };

  // merge options
  for (var attrName in options) {
    if (options.hasOwnProperty(attrName)) {
      this.options[attrName] = options[attrName];
    }
  }

  // verify that wrapper is an Element, else return early
  if (!wrapper.tagName) {
    console.warn('Globalicious: `wrapper` is not an Element');
    return;
  }

  this.width = parseInt(window.getComputedStyle(wrapper).width, 10);
  this.height = this.width;
  this.speed = this.options.speed;
  this.incline = this.options.incline;
  this.start = Date.now();
  this.paused = 0;
  this.rotationX = this.rotationXCache = this.options.startingRotation;
  this.goto = false;

  this.sphere = { type: 'Sphere' };
  this.land = null;
  this.borders = null;
  this.grid = null;
  this.markers = [];

  var marginAside = 20;
  var marginTop = 30;

  // projection allows us to choose a projection type ('orthographic'
  // here) and define some base settings. scale() determines the zoom
  // level, and it needs to grow or shrink in conjunction with the
  // window size.
  this.projection = d3
    .geoOrthographic()
    .scale((this.width - marginAside * 2) / 2)
    .translate([this.width / 2, (this.height + marginTop) / 2])
    .clipAngle(90)
    .precision(0.5);

  // graticule is simply the parallels and meridians lines
  this.graticule = d3.geoGraticule();

  // other than setting our canvas' dimensions, we define the drag
  // behaviour of our map
  this.canvas = d3.select(wrapper).append('canvas');

  var canvasEl = this.canvas.node();
  var canvasScale = window.devicePixelRatio; // Change to 1 on retina screens to see blurry canvas.

  // context is our means for interacting with the drawing layer of the
  // canvas element we’ve added to the page.
  this.context = canvasEl.getContext('2d');

  // set canvas display size (css pixels), then set actual size
  // (scaled to account for extra pixel density) and normalize scale
  canvasEl.style.width = this.width + 'px';
  canvasEl.style.height = this.height + 'px';
  canvasEl.width = this.width * canvasScale;
  canvasEl.height = this.height * canvasScale;
  this.context.scale(canvasScale, canvasScale);

  // path is our path generator, which will convert GeoJSON to a usable
  // format with our specified projection. By default, this would return
  // a SVG path string, but adding context(this.context) forces it to
  // operate in the context of our canvas.
  this.path = d3
    .geoPath()
    .projection(this.projection)
    .context(this.context);

  // we use pointPath to determine if the markers are still on the visible
  // side of the globe
  this.pointPath = d3
    .geoPath()
    .projection(this.projection)
    .pointRadius(function(/* d */) {
      return 6;
    });

  // we use the svg to draw the markers
  this.svg = d3
    .select(wrapper)
    .append('svg')
    .attr('width', this.width)
    .attr('height', this.height)
    .attr('style', 'position: absolute; top: 0; left: 0')
    .call(
      d3
        .drag()
        .subject(function() {
          return {
            x: self.rotationX * 2,
            y: -self.incline * 2,
          };
        })
        .on('start', function() {
          self.paused |= 1; // 4 > 5; 5 > 5; 1 > 1; 0 > 1
        })
        .on('drag', function() {
          self.rotationX = d3.event.x / 2;
          self.incline = -d3.event.y / 2;
          self.rotate(self.rotationX, self.incline);
        })
        .on('end', function() {
          self.paused &= -2; // 5 > 4; 4 > 4; 1 > 0; 0 > 0
          self.start = Date.now();
          self.rotationXCache = self.rotationX;
        })
    )
    .append('g');

  this.svg
    .append('rect')
    .attr('class', 'glbl-overlay')
    .attr('width', this.width)
    .attr('height', this.height)
    .attr('fill', 'transparent');

  d3.json(this.options.data)
    .then(function jsonData(topo) {
      self.land = topojson.feature(topo, topo.objects.land);
      self.borders = topojson.mesh(topo, topo.objects.countries, function(
        a,
        b
      ) {
        return a !== b;
      });
      self.grid = self.graticule();

      // at least once
      self.rotate(self.rotationX, self.incline);

      d3.timer(function rotate() {
        if (self.goto) {
          var long = -self.goto.long;
          var lat = -self.goto.lat;
          var distanceX = (self.rotationXCache % 360) - long;
          var distanceY = (self.incline % 360) - lat;
          var elapsed = (Date.now() - self.start) / self.options.goToDuration; // [0, 1]
          if (elapsed < 1) {
            var rotationX =
              self.rotationXCache -
              Easing[self.options.goToEasing](elapsed) * distanceX;
            var rotationY =
              self.incline -
              Easing[self.options.goToEasing](elapsed) * distanceY;
            self.rotate(rotationX, rotationY);
          } else {
            self.rotate(long, lat);
            self.rotationXCache = self.rotationX = long;
            self.incline = lat;
            if (typeof self.goto.callback === 'function') self.goto.callback();
            self.goto = false;
          }
        } else if (self.speed) {
          if (self.paused) return;

          var speed = self.speed * 1e-2;
          self.rotationX =
            self.rotationXCache + speed * (Date.now() - self.start);
          self.rotate(self.rotationX, self.incline);
        }
      });
    })
    .catch(error => console.error('Globalicious: ' + error));

  this.rotate = function(long, lat) {
    this.projection.rotate([long, lat]);

    this.context.clearRect(0, 0, this.width, this.height);

    if (this.options.outline) {
      this.context.beginPath();
      this.path(this.sphere);
      this.context.lineWidth = Math.round(this.width * 2) / 300;
      this.context.strokeStyle = this.options.outline;
      this.context.stroke();
    }

    if (this.options.sea) {
      this.context.beginPath();
      this.path(this.sphere);
      this.context.fillStyle = this.options.sea;
      this.context.fill();
    }

    if (this.options.gridUnder) {
      this.context.beginPath();
      this.path(this.grid);
      this.context.lineWidth = 0.5;
      this.context.strokeStyle = this.options.gridUnder;
      this.context.stroke();
    }

    if (this.options.borders) {
      this.context.beginPath();
      this.path(this.borders);
      this.context.lineWidth = 0.5;
      this.context.strokeStyle = this.options.borders;
      this.context.stroke();
    }

    if (this.options.land) {
      this.context.beginPath();
      this.path(this.land);
      this.context.fillStyle = this.options.land;
      this.context.fill();
    }

    if (this.options.gridOver) {
      this.context.beginPath();
      this.path(this.grid);
      this.context.lineWidth = 0.5;
      this.context.strokeStyle = this.options.gridOver;
      this.context.stroke();
    }

    this.svg
      .selectAll('g.glbl-marker')
      .attr('transform', function(datum /* , index */) {
        var proj = self.projection([datum[0], datum[1]]);
        return 'translate(' + proj[0] + ',' + proj[1] + ')';
      })
      .classed('is-hidden', function(datum /* , index */) {
        return (
          self.pointPath({
            type: 'Point',
            coordinates: [datum[0], datum[1]],
          }) === null
        );
      });
  };
};

// API
Globalicious.prototype.pause = function() {
  this.paused |= 4; // 0 > 4; 1 > 5; 4 > 4; 5 > 5
  this.rotationXCache = this.rotationX;
};

Globalicious.prototype.play = function() {
  this.paused &= -5; // 4 > 0; 5 > 1; 0 > 0; 1 > 1
  this.start = Date.now();
};

Globalicious.prototype.setSpeed = function(speed) {
  this.rotationXCache = this.rotationX;
  this.start = Date.now();
  this.speed = parseInt(speed, 10);
};

Globalicious.prototype.goTo = function(lat, long, callback) {
  this.pause();
  this.rotationXCache = this.rotationX;
  this.start = Date.now();
  this.goto = {
    lat: lat,
    long: long,
    callback: callback,
  };
};

Globalicious.prototype.mark = function(lat, long, shape, onClick) {
  var self = this;

  // so that we can rotate them
  this.markers.push([long, lat, shape]);

  this.svg
    .selectAll('g.glbl-marker')
    .data(this.markers)
    .enter()
    .append('g')
    .attr('class', 'glbl-marker')
    .attr('id', function(datum /* , index */) {
      var transLat = datum[1].toString().replace('.', '♥');
      var transLong = datum[0].toString().replace('.', '♥');
      return 'glbl-marker_' + transLat + '_' + transLong;
    })
    .each(function(datum /* , index */) {
      var g = d3.select(this).append('g');
      var xml = parser.parseFromString(datum[2], 'image/svg+xml');
      var importedNode = document.importNode(xml.documentElement, true);
      importedNode.setAttribute('x', -(importedNode.getAttribute('width') / 2));
      importedNode.setAttribute('y', -importedNode.getAttribute('height'));
      g.node().appendChild(importedNode);
    })
    .attr('transform', function(datum /* , index */) {
      var proj = self.projection([datum[0], datum[1]]);
      return 'translate(' + proj[0] + ',' + proj[1] + ')';
    })
    .on('click', function() {
      if (typeof onClick === 'function') {
        onClick.call(this, lat, long);
      }
    });
};

export default Globalicious;
