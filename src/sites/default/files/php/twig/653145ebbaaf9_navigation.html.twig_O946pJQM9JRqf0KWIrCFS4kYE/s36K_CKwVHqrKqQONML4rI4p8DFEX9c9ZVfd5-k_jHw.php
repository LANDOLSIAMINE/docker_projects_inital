<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* themes/contrib/gin/templates/navigation.html.twig */
class __TwigTemplate_b06d1c3afd55c3a918898f8177c6310f extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<aside class=\"admin-toolbar\" id=\"admin-toolbar\">
  ";
        // line 3
        echo "  <div class=\"admin-toolbar__displace-placeholder\"></div>
  ";
        // line 5
        echo "  ";
        if (($context["icon_path"] ?? null)) {
            // line 6
            echo "  <div class=\"admin-toolbar__header\">
    <a class=\"admin-toolbar__logo\" href=\"/\">
      <img alt=\"";
            // line 8
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
            echo "\" src=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getFileUrl($this->sandbox->ensureToStringAllowed(($context["icon_path"] ?? null), 8, $this->source)), "html", null, true);
            echo "\" loading=\"eager\" width=\" 32\" height=\" 32\">
    </a>
  </div>
  ";
        }
        // line 12
        echo "  ";
        // line 13
        echo "
  ";
        // line 14
        $context["title_menu"] = "admin-toolbar";
        // line 15
        echo "  <h2 id=\"";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title_menu"] ?? null), 15, $this->source), "html", null, true);
        echo "\" class=\"visually-hidden\">";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Administrative toolbar"));
        echo "</h2>

  ";
        // line 18
        echo "  <nav id=\"menu-builder\" class=\"admin-toolbar__content\" aria-labelledby=\"";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title_menu"] ?? null), 18, $this->source), "html", null, true);
        echo "\">
    ";
        // line 20
        echo "    ";
        if ( !($context["icon_path"] ?? null)) {
            // line 21
            echo "    <div class=\"navigation-menu-wrapper menu--logo\">
      <h2 id=\"menu--logo\" class=\"menu-title visually-hidden\">";
            // line 22
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
            echo "</h2>
      <ul class=\"toolbar-menu\" data-once=\"toolbar-menu\" aria-labelledby=\"menu--logo\">
        <li class=\"toolbar-menu__item toolbar-menu__item--level-1\">
          <a href=\"/\" class=\"toolbar-link toolbar-link--has-icon toolbar-link--gin-home\">
            <span>";
            // line 26
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Home"));
            echo "</span>
          </a>
        </li>
      </ul>
    </div>
    ";
        }
        // line 32
        echo "    ";
        // line 33
        echo "    ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["menu_top"] ?? null), 33, $this->source), "html", null, true);
        echo "
    ";
        // line 34
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["menu_middle"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["section"]) {
            // line 35
            echo "      ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed($context["section"], 35, $this->source), "html", null, true);
            echo "
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['section'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        echo "  </nav>

  ";
        // line 40
        echo "  <div class=\"admin-toolbar__sticky-section\">
    ";
        // line 41
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["menu_bottom"] ?? null), 41, $this->source), "html", null, true);
        echo "
  </div>
</aside>
<script>
  if (localStorage.getItem('Drupal.navigation.sidebarExpanded') !== 'false') {
    document.documentElement.classList.add('admin-toolbar-expanded');
  }
</script>
";
    }

    public function getTemplateName()
    {
        return "themes/contrib/gin/templates/navigation.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  130 => 41,  127 => 40,  123 => 37,  114 => 35,  110 => 34,  105 => 33,  103 => 32,  94 => 26,  87 => 22,  84 => 21,  81 => 20,  76 => 18,  68 => 15,  66 => 14,  63 => 13,  61 => 12,  52 => 8,  48 => 6,  45 => 5,  42 => 3,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/contrib/gin/templates/navigation.html.twig", "/opt/drupal/web/themes/contrib/gin/templates/navigation.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 5, "set" => 14, "for" => 34);
        static $filters = array("t" => 8, "escape" => 8);
        static $functions = array("file_url" => 8);

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set', 'for'],
                ['t', 'escape'],
                ['file_url']
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
