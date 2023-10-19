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

/* themes/contrib/gin/templates/menu-region--bottom.html.twig */
class __TwigTemplate_b5fd380137cbbbfbdbfed6b67cb5f535 extends Template
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
        $context["item_id"] = ("item-" . $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 1, $this->source));
        // line 2
        echo "
<div class=\"admin-toolbar__item toolbar-block\">
  <ul class=\"toolbar-menu\">
    <li id=\"";
        // line 5
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["item_id"] ?? null), 5, $this->source), "html", null, true);
        echo "\" class=\"toolbar-menu__item toolbar-menu__item--level-1\">
      <button aria-controls=\"admin-toolbar\" aria-expanded=\"true\" class=\"toolbar-link toolbar-link--has-icon toolbar-link--sidebar-toggle sidebar-toggle\">
        ";
        // line 8
        echo "        <span id=\"sidebar-state\" class=\"toolbar-link__label\">";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Collapse sidebar"));
        echo "</span>
      </button>
    </li>
    <li class=\"toolbar-menu__item toolbar-menu__item--level-1\">
      <a href=\"";
        // line 12
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->getPath("help.main"));
        echo "\" class=\"toolbar-link toolbar-link--has-icon toolbar-link--help\">
        <span>";
        // line 13
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Help"));
        echo "</span>
      </a>
    </li>
    <li class=\"toolbar-menu__item toolbar-menu__item--has-dropdown toolbar-menu__item--user toolbar-menu__item--level-1\">
      <button class=\"toolbar-link toolbar-link--has-icon toolbar-link--";
        // line 17
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["menu_name"] ?? null), 17, $this->source)), "html", null, true);
        echo "\" data-url=\"";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["url"] ?? null), 17, $this->source), "html", null, true);
        echo "\">
        <span class=\"toolbar-link__action\">";
        // line 18
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Extend"));
        echo "</span>
        <span class=\"toolbar-link__label\">";
        // line 19
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 19, $this->source), "html", null, true);
        echo "</span>
      </button>
      <div class=\"toolbar-menu__submenu\">
        <div class=\"toolbar-menu__arrow-ref\"></div>
        <ul class=\"toolbar-menu\">
          <li class=\"toolbar-menu__item toolbar-menu__item--to-title\">
            <button class=\"toolbar-link toolbar-link--has-icon toolbar-link--";
        // line 25
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["menu_name"] ?? null), 25, $this->source)), "html", null, true);
        echo "\" tabindex=\"-1\">
              <span class=\"toolbar-link__action\">";
        // line 26
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Extend"));
        echo "</span>
              <span class=\"toolbar-link__label\">";
        // line 27
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 27, $this->source), "html", null, true);
        echo "</span>
            </button>
          </li>
          ";
        // line 30
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 31
            echo "            <li class=\"toolbar-menu__item toolbar-menu__item--level-2\">
              <a href=\"";
            // line 32
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 32), 32, $this->source), "html", null, true);
            echo "\" class=\"toolbar-link\">
                <span>";
            // line 33
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 33), 33, $this->source), "html", null, true);
            echo "</span>
              </a>
            </li>
          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 37
        echo "        </ul>
      </div>
    </li>
  </ul>
</div>
";
    }

    public function getTemplateName()
    {
        return "themes/contrib/gin/templates/menu-region--bottom.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  124 => 37,  114 => 33,  110 => 32,  107 => 31,  103 => 30,  97 => 27,  93 => 26,  89 => 25,  80 => 19,  76 => 18,  70 => 17,  63 => 13,  59 => 12,  51 => 8,  46 => 5,  41 => 2,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/contrib/gin/templates/menu-region--bottom.html.twig", "/opt/drupal/web/themes/contrib/gin/templates/menu-region--bottom.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 1, "for" => 30);
        static $filters = array("escape" => 5, "t" => 8, "clean_class" => 17);
        static $functions = array("path" => 12);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'for'],
                ['escape', 't', 'clean_class'],
                ['path']
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
