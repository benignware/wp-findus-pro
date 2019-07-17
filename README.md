# wp-findus

> Create contact maps easily

This plugin lets you quickly add a map to your posts or sidebar by providing a location or some geocodeable content.

It is doing so by integrating [jquery-findus](http://benignware.github.io/jquery-findus/) of which a copy is bundled with this distribution.  

## Install

Copy to your plugins directory manually or install from git via composer.

## Usage

### Shortcode

Wrap some geocodeable content into the findus shortcode
```
[findus]
8411 Market Street
San Francisco
CA 94103
USA
[/findus]
```

Resolve an address from a location:

```
[findus latitude="37.77485730000001" longitude="-122.41962339999998"/]
```

Avoid geocoding by providing location and content:

```
[findus latitude="37.77485730000001" longitude="-122.41962339999998"]
8411 Market Street
San Francisco
CA 94103
USA
[/findus]
```


### Widget

Simply drag the FindUs-Widget to your sidebar and enter title and some geocodeable content.


## API

### Options

<table>
  <tr>
    <th>Name</th><th>Description</th><th>Default</th>
  </tr>
  <tr>
    <td>address</td><td>A geocodeable address string</td><td></td>
  </tr>
  <tr>
    <td>api_key</td><td>Provide an api-key for the Google Maps API</td><td></td>
  </tr>
  <tr>
    <td>latitude</td><td>Location coordinate latitude</td><td></td>
  </tr>
  <tr>
    <td>longitude</td><td>Location coordinate longitude</td><td></td>
  </tr>
</table>


## Development

Download [Docker CE](https://www.docker.com/get-docker) for your OS.
Download [NodeJS](https://nodejs.org) for your OS.

### Install

#### Install wordpress

```cli
docker-compose run --rm wp install-wp
```

After installation you can log in with user `wordpress` and password `wordpress`.

#### Install front-end dependencies

```cli
npm i
```

### Development Server

Point terminal to your project root and start up the container.

```cli
docker-compose up -d
```

Point your browser to [http://localhost:8030](http://localhost:8030).


#### Watch front-end dependencies

```cli
npm run watch
```

### Docker

##### Update composer dependencies

```cli
docker-compose run composer update
```

##### Globally stop all running docker containers

```cli
docker stop $(docker ps -a -q)
```

##### Update Wordpress

Due to some permission issues, you need to chmod your container's web-root prior to running the updater:

```cli
docker-compose exec wordpress bash
```

From the container shell, change permissons all down the tree.
```cli
chmod -R 777 .
```

After `CTRL+D`, you're ready to update Wordpress, either from the admin-interface or using wp-cli:

```
docker-compose run wp core update
```
