<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config.title }} - BGP Info</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <style type="text/css">
      #content {
        padding-top: 60px;
      }
    </style>
  </head>

  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="#">{{ config.title }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item{% block peersactive %}{% endblock %}">
              <a class="nav-link" href="/peers/">Peers <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item{% block lgactive %}{% endblock %}">
              <a class="nav-link" href="?site=lg">Looking Glass</a>
            </li>
            {% if config.policy %}
            <li class="nav-item">
              <a class="nav-link" href="{{ config.policy }}">Peering Policy</a>
            </li>
            {% endif %}
          </ul>



          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ active }} </a>
              <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                {% for key, value in routers if value.name != active %}
                  {% if request.site %}
                    <a class="dropdown-item" href="?site={{ request.site }}&router={{ key }}">{{ value.name }}</a>
                  {% else %}
                     <a class="dropdown-item" href="?router={{ key }}">{{ value.name }}</a>
                  {% endif %}
                {% endfor %}
              </div>
            </li>
          </ul>



        </div>
      </div>
    </nav>

    <div class="container" id="content">
{% block title %}
{% endblock %}
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">BGP</a></li>
          <li class="breadcrumb-item active" aria-current="page">{% block page %}{% endblock %}</li>
        </ol>
      </nav>
{% block content %}
{% endblock %}
      <footer class="border-top">
        {% block footer %}
        <p class="text-muted">Looking Glass {{ config.version|default("n/a") }}</p>
        {% endblock %}
      </footer>
    </div>
  </body>
