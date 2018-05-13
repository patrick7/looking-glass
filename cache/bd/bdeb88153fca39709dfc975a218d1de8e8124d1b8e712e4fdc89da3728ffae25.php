<?php

/* index.html */
class __TwigTemplate_0c5cfb57a54508f9141f59742ff7d8cff1aae806fdb133fb8be795204553fa16 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<html lang=\"en\">
  <head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
    <title>BGP Sessions @ AS62078</title>

    <!-- Bootstrap core CSS -->
    <link href=\"bootstrap/css/bootstrap.min.css\" rel=\"stylesheet\">
    <script src=\"https://code.jquery.com/jquery-3.3.1.slim.min.js\" integrity=\"sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo\" crossorigin=\"anonymous\"></script>
    <script src=\"bootstrap/js/bootstrap.min.js\"></script>
  </head>

  <body>
    <div class=\"d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow\">
      <h5 class=\"my-0 mr-md-auto font-weight-normal\">AS62078</h5>
      <nav class=\"my-2 my-md-0 mr-md-3\">
        <a class=\"p-2 text-dark\" href=\"#\">Looking Glass</a>
        <a class=\"p-2 text-dark\" href=\"https://www.as62078.net/\">Peering Policy</a>
      </nav>
    </div>

    <div class=\"container\">
      <h1>BGP Sessions</h1>
      <nav aria-label=\"breadcrumb\">
        <ol class=\"breadcrumb\">
          <li class=\"breadcrumb-item\"><a href=\"#\">BGP</a></li>
          <li class=\"breadcrumb-item active\" aria-current=\"page\">Sessions</li>
        </ol>
      </nav>

      <table class=\"table\">
        <thead class=\"thead-dark\">
          <tr>
            <th scope=\"col\">AS</th>
            <th scope=\"col\">Neighbor IP</th>
            <th scope=\"col\">State</th>
            <th scope=\"col\">Uptime</th>
            <th scope=\"col\">Prefix Count</th>
            <th scope=\"col\">msgRcvd</th>
            <th scope=\"col\">msgSent</th>
            <th scope=\"col\">Peer Group</th>
            <th scope=\"col\">Details</th>
        </tr>
      </thead>

";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function getDebugInfo()
    {
        return array (  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "index.html", "/var/www/velder.li/htdocs/peers/templates/index.html");
    }
}
