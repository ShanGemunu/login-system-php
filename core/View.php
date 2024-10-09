<?php

namespace app\core;

use app\core\Log;

class View
{
    /** 
     *    get relevent view, place it inside relvent layout 
     *    and return whole view
     *    @param string $view, array $params
     *    @param array $params
     *    @return string
     */
    public function renderView($view, array $params): string
    {
        $layoutName = Application::$app->layout;
        if (isset(Application::$app->controller)) {
            $layoutName = Application::$app->controller->layout;
        }
        $viewContent = $this->renderViewOnly($view, $params);
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layoutName.php";
        $layoutContent = ob_get_clean();
        $layoutContent = str_replace('{{title}}', ucfirst($view), $layoutContent);
        Log::logInfo("View", "renderView", stepDescription: "return view with layout", stepStatus: "success", data: "view - $view, params - ...");

        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /** 
     *    get relevent view with parameters and return it
     *    @param string $view, array $params
     *    @param array $params
     *    @return string    
     */
    public function renderViewOnly(string $view, array $params): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        Log::logInfo("View", "renderViewOnly", "return only view", "success", "view - $view, params - ...");

        return ob_get_clean();
    }

    public function buildCustomComponent(string $component, string $componentId, array $params = []): string
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include Application::$ROOT_DIR . "/views/components/$component.php";
        $component = ob_get_clean();
        $component = str_replace('{{componentId}}', $componentId, $component);

        return $component;
    }
}