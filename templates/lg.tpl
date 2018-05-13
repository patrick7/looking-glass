{% extends "base.tpl" %}

{% block lgactive %} active{% endblock %}

{% block title %}
<h1>Looking Glass</h1>
{% endblock %}

{% block page %}
Looking Glass
{% endblock %}

{% block content %}

{% for error in errors %}
  {% set errortext = {
    'argument-empty': 'Please enter an argument.',
    'argument-invalid': 'Please enter a valid argument (IPv4 / IPv6 address or prefix).',
    'argument-notset': 'No argument was provided.',
    'command-empty': 'No command was selected',
    'command-notset': 'No command was provided',
    'afi-ipv4-prefix-ipv6': 'Prefix is IPv6 but the command is IPv4',
    'afi-ipv6-prefix-ipv4': 'Prefix is IPv4 but the command is IPv6',
  }[error] ?? ('Unknown error: ' ~ error) %}


  <div class="alert alert-danger" role="alert">
    <b>Error:</b> {{ errortext }}
  </div>
{% endfor %}

<form action="" method="POST">
  <div class="row">
    <div class="col-sm">
      <label for="command">Command</label>
      <select class="custom-select" id="command" name="command">
        <option {% if request.command == "shbgpipv4" %}selected {% endif %}value="shbgpipv4">sh bgp ipv4</option>
        <option {% if request.command == "shbgpipv6" %}selected {% endif %}value="shbgpipv6">sh bgp ipv6</option>
      </select>
    </div>

    <div class="col-sm">
      <label for="argument">Argument</label>
      <div class="input-group mb-3">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1">Argument</span>
        </div>
        <input type="text" class="form-control" placeholder="8.8.8.8" aria-label="Argument" aria-describedby="basic-addon1" name="argument" value="{{ request.argument }}">
      </div>
      <button type="submit" class="btn btn-primary float-right">Submit</button>
    </div>
  </div>
</form>
{% if data|length > 0 %}
<br>
<div class="alert alert-primary" role="alert">
  <b>BGP Prefix:</b> {{ data.prefix }}
</div>
    {% for path in data.paths %}
    <h3><span class="badge badge-{% if path.bestpath.overall %}primary{% else %}secondary{% endif %}">Path {{ loop.index }} </span></h3>
    <table class="table table-striped">
      <tr>
        <th style="width: 25%;">AS_PATH</th>
        <td>{{ path.aspath.string }} <span class="badge badge-info">{{ path.origin }}</span></td>
      </tr>
      {% if path.bestpath.overall %}
      <tr>
        <th style="width: 25%;">Best Path</th>
        <td>True</td>
      </tr>
      {% endif %}
      <tr>
        <th style="width: 25%;">Local Preference</th>
        <td>{{ path.localpref }}</td>
      </tr>
      {% if path.med %}
      <tr>
        <th style="width: 25%;">Med</th>
        <td>{{ path.med }}</td>
      </tr>
      {% endif %}
      {% if path.community.string %}
      <tr>
        <th style="width: 25%;">Communities</th>
        <td>{{ path.community.string }}</td>
      </tr>
      {% endif %}
      {% if path.largeCommunity.string %}
      <tr>
        <th style="width: 25%;">Large Communities</th>
        <td>{{ path.largeCommunity.string }}</td>
      </tr>
      {% endif %}
      <tr>
        <th style="width: 25%">Received from</th>
        <td>{{ path.peer.peerId }}</td>
      </tr>
      <tr>
        <th style="width: 25%">Next Hops</th>
        <td>
        {% for nexthop in path.nexthops %}
          {% if nexthop.accessible %}
            <span class="badge badge-primary">Accessible</span>
          {% else %}
            <span class="badge badge-danger">Inaccessible</span>
          {% endif %}
          {% if nexthop.used %}
            <span class="badge badge-success">Used</span>
          {% endif %}
          {{ nexthop.ip }}
          <br>
        {% endfor %}
        </td>
      </tr>
      <tr>
        <th style="width: 25%;">Last Update</th>
        <td>{{ path.lastUpdate.string }}</td>
      </tr>
    </table>
    <br>
    {% endfor %}
</table>

{% endif %}
{% endblock %}
