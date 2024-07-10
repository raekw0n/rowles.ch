<?php

namespace App\Controllers;

use Psr\Log\LoggerInterface;
use App\Contracts\ViewEngineInterface;

/**
 * Abstract base controller class.
 */
abstract class Controller
{
    /** @var LoggerInterface $log */
    protected $log;

    /** @var ViewEngineInterface $view */
    protected $view;

    /** @var array $data */
    public array $data = [];

    /**
     * Abstract base Controller constructor.
     *
     * @param LoggerInterface $logger
     * @param ViewEngineInterface $view
     */
    public function __construct(LoggerInterface $logger, ViewEngineInterface $view)
    {
        $this->log = $logger;
        $this->view = $view;

        $this->data['title'] = env("APP_NAME");
    }

    /**
     * Set data to pass to the view.
     *
     * @param array $data
     * @return self
     */
    protected function setViewData(array $data = []) : self
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if ($key === 'title' && $val !== '') {
                    $val = $val . ' | ' . $this->data['title'];
                }
                $this->data[$key] = $val;
            }
        }

        return $this;
    }

    /**
     * Renders templates with view data.
     *
     * @param string $template
     * @return mixed
     */
    protected function render(string $template)
    {
        $template = $this->getTemplate($template);

        return $this->view->render($template, $this->data);
    }

    /**
     * Get template.
     *
     * @param string $template
     * @return string
     */
    private function getTemplate(string $template): string
    {
        if(strpos($template, '.twig') === false) {
            return $template.'.twig';
        }

        return $template;
    }
}
