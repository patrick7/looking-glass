{% extends "base.tpl" %}

{% block peersactive %} active{% endblock %}

{% block title %}
<h1>BGP Sessions</h1>
{% endblock %}

{% block page %}
Peering Sessions
{% endblock %}


{% block content %}

      <table class="table">
        <thead class="thead-dark">
          <tr>
            <th scope="col">AS</th>
            <th scope="col">Neighbor IP</th>
            <th scope="col">State</th>
            <th scope="col">Uptime</th>
            <th scope="col">Prefix Count</th>
            <th scope="col">Peer Group</th>
            <th scope="col">Details</th>
        </tr>
      </thead>

{% for asn, sessions in data %}
  {% for session, parameter in sessions %}

{% if parameter.addressFamilyInfo['IPv4 Unicast']|length > 0 %}
  {% set afibase = parameter.addressFamilyInfo['IPv4 Unicast'] %}
  {% set afi = "IPv4" %}
  {% set style = "secondary" %}
{% else %}
  {% if parameter.addressFamilyInfo['IPv6 Unicast']|length > 0 %}
    {% set afibase = parameter.addressFamilyInfo['IPv6 Unicast'] %}
    {% set afi = "IPv6" %}
    {% set style = "primary" %}
  {% endif %}
{% endif %}

    <tr>
    {% if loop.first %}
      <td rowspan="{{ sessions|length }}">{{ asn }}</td>
    {% endif %}
      <td><span class="badge badge-{{ style }}">{{ afi }}</span> {{ session }}</td>
      {% if parameter.bgpState == "Established" %}
        <td><span class="badge badge-success">Established</span></td>
      {% else %}
        <td><span class="badge badge-warning">{{ parameter.bgpState }}</span></td>
      {% endif %}
      <td>{{ parameter.bgpTimerUpString }}</td>
      <td>{{ afibase.acceptedPrefixCounter }}</td>
      <td>{{ afibase.peerGroupMember }}</td>
      <td><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#peer{{ parameter.sessionid }}">Details</button></td>
    </tr>
  {% endfor %}
{% endfor %}
  </table>

{% for asn, sessions in data %}
  {% for session, parameter in sessions %}
<div class="modal fade" id="peer{{ parameter.sessionid }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">AS{{ asn }} - {{ session }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {{ parameter.nbrDesc }}
        <table class="table table-striped">
          <tr>
            <td>Remote ID</td>
            <td>{{ parameter.remoteRouterId }}</td>
          </tr>
          <tr>
            <td>Updates received</td>
            <td>{{ parameter.messageStats.updatesRecv }}</td>
          </tr>
          <tr>
            <td>Updates sent</td>
            <td>{{ parameter.messageStats.updatesSent }}</td>
          </tr>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
  {% endfor %}
{% endfor %}






{% endblock %}
