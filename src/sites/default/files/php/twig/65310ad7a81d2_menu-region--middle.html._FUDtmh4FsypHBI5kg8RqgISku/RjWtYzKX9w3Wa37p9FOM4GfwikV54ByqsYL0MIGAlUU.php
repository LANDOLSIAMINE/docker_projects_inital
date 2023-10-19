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

/* themes/contrib/gin/templates/menu-region--middle.html.twig */
class __TwigTemplate_a214d1fbab47d7d06334b26ca4c0f00b extends Template
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
        $macros["menus"] = $this->macros["menus"] = $this;
        // line 2
        echo "<div class=\"admin-toolbar__item toolbar-block\">
  ";
        // line 3
        $context["menu_heading_id"] = ("menu--" . $this->sandbox->ensureToStringAllowed(($context["menu_name"] ?? null), 3, $this->source));
        // line 4
        echo "  <h2 id=\"";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["menu_heading_id"] ?? null), 4, $this->source), "html", null, true);
        echo "\" class=\"toolbar-block__title visually-hidden focusable\">";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 4, $this->source), "html", null, true);
        echo "</h2>
  <ul class=\"toolbar-menu toolbar-menu toolbar-block__content\" aria-toolbar-link__labelledby=\"";
        // line 5
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["menu_heading_id"] ?? null), 5, $this->source), "html", null, true);
        echo "\">
    ";
        // line 6
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["menus"], "macro_menu_items", [($context["items"] ?? null), ($context["attribute"] ?? null)], 6, $context, $this->getSourceContext()));
        echo "
  </ul>
</div>

";
    }

    // line 10
    public function macro_menu_items($__items__ = null, $__attributes__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "items" => $__items__,
            "attributes" => $__attributes__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start(function () { return ''; });
        try {
            // line 11
            echo "  ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 12
                echo "  ";
                $context["item_id"] = ("item-" . $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 12), 12, $this->source));
                // line 13
                echo "    <li id=\"";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["item_id"] ?? null), 13, $this->source), "html", null, true);
                echo "\" class=\"";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(((twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 13)) ? ("toolbar-menu__item--has-dropdown") : ("")));
                echo " toolbar-menu__item toolbar-menu__item--level-1\" data-url=\"";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 13), 13, $this->source), "html", null, true);
                echo "\">
      ";
                // line 14
                if (twig_test_empty(twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 14))) {
                    // line 15
                    echo "        <a href=\"";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 15), 15, $this->source), "html", null, true);
                    echo "\" class=\"toolbar-link toolbar-link--has-icon toolbar-link--";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 15), 15, $this->source)), "html", null, true);
                    echo "\">
          <span>";
                    // line 16
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 16), 16, $this->source), "html", null, true);
                    echo "</span>
        </a>
      ";
                }
                // line 19
                echo "    ";
                if (twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 19)) {
                    // line 20
                    echo "      <button class=\"toolbar-link toolbar-link--has-icon toolbar-link--";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 20), 20, $this->source)), "html", null, true);
                    echo "\">
        <span class=\"toolbar-link__action\">";
                    // line 21
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Extend"));
                    echo "</span>
        <span class=\"toolbar-link__label\">";
                    // line 22
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
                    echo "</span>
      </button>
      <div class=\"toolbar-menu__submenu\">
        <div class=\"toolbar-menu__arrow-ref\"></div>
        <ul class=\"toolbar-menu\">
          <li class=\"toolbar-menu__item toolbar-menu__item--to-title\">
            <button class=\"toolbar-link toolbar-link--has-icon toolbar-link--";
                    // line 28
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 28), 28, $this->source)), "html", null, true);
                    echo "\" tabindex=\"-1\" data-url=\"";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 28), 28, $this->source), "html", null, true);
                    echo "\">
              <span class=\"toolbar-link__action\">";
                    // line 29
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Extend"));
                    echo "</span>
              <span class=\"toolbar-link__label\">";
                    // line 30
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 30), 30, $this->source), "html", null, true);
                    echo "</span>
            </button>
          </li>
          ";
                    // line 33
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 33));
                    foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                        // line 34
                        echo "            <li class=\"";
                        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(((twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 34)) ? ("toolbar-menu__item--has-dropdown") : ("")));
                        echo " toolbar-menu__item toolbar-menu__item--level-2\">
              ";
                        // line 35
                        if (twig_test_empty(twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 35))) {
                            // line 36
                            echo "                <a href=\"";
                            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 36), 36, $this->source), "html", null, true);
                            echo "\" class=\"toolbar-link\">
                <span>";
                            // line 37
                            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 37), 37, $this->source), "html", null, true);
                            echo "</span>
              </a>
              ";
                        }
                        // line 40
                        echo "              ";
                        if (twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 40)) {
                            // line 41
                            echo "                <button class=\"toolbar-link\" aria-expanded=\"false\">
                  <span class=\"toolbar-link__action\">";
                            // line 42
                            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Extend"));
                            echo "</span>
                  <span class=\"toolbar-link__label\">";
                            // line 43
                            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 43), 43, $this->source), "html", null, true);
                            echo "</span>
                </button>
                <ul class=\"toolbar-menu\">
                  ";
                            // line 46
                            $context['_parent'] = $context;
                            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "below", [], "any", false, false, true, 46));
                            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                                // line 47
                                echo "                    <li class=\"toolbar-menu__item toolbar-menu__item--level-3\">
                      <a href=\"";
                                // line 48
                                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "url", [], "any", false, false, true, 48), 48, $this->source), "html", null, true);
                                echo "\" class=\"toolbar-link\">
                        <span class=\"toolbar-link__label\">";
                                // line 49
                                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, true, 49), 49, $this->source), "html", null, true);
                                echo "</span>
                      </a>
                    </li>
                  ";
                            }
                            $_parent = $context['_parent'];
                            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                            $context = array_intersect_key($context, $_parent) + $_parent;
                            // line 53
                            echo "                </ul>
              ";
                        }
                        // line 55
                        echo "            </li>
          ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    // line 57
                    echo "        </ul>
      </div>
    ";
                }
                // line 60
                echo "  </li>
  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;

            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    public function getTemplateName()
    {
        return "themes/contrib/gin/templates/menu-region--middle.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  224 => 60,  219 => 57,  212 => 55,  208 => 53,  198 => 49,  194 => 48,  191 => 47,  187 => 46,  181 => 43,  177 => 42,  174 => 41,  171 => 40,  165 => 37,  160 => 36,  158 => 35,  153 => 34,  149 => 33,  143 => 30,  139 => 29,  133 => 28,  124 => 22,  120 => 21,  115 => 20,  112 => 19,  106 => 16,  99 => 15,  97 => 14,  88 => 13,  85 => 12,  80 => 11,  66 => 10,  57 => 6,  53 => 5,  46 => 4,  44 => 3,  41 => 2,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/contrib/gin/templates/menu-region--middle.html.twig", "/opt/drupal/web/themes/contrib/gin/templates/menu-region--middle.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("import" => 1, "set" => 3, "macro" => 10, "for" => 11, "if" => 14);
        static $filters = array("escape" => 4, "clean_class" => 15, "t" => 21);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['import', 'set', 'macro', 'for', 'if'],
                ['escape', 'clean_class', 't'],
                []
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
