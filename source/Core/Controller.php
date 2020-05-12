<?php

namespace Source\Core;

use Source\Support\Message;

/**
 * Staut | Class Controller
 *
 * @author Lucas Natan S. Gonçalves <lucasnatan@live.com>
 * @package Source\Models
 */
class Controller
{
    /** @var View */
    protected $view;

    /** @var Message */
    protected $message;

    /**
     * Controller constructor.
     * @param string|null $pathToViews
     */
    public function __construct(string $pathToViews = null)
    {
        $this->view = new View($pathToViews);
        $this->message = new Message();
    }
}