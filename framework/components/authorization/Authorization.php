<?php

namespace pwf\components\authorization;

use pwf\basic\Component;
use pwf\basic\Application;
use pwf\components\authorization\interfaces\Identity;

class Authorization extends Component
{
    const TOKEN_NAME = 'access-token';

    /**
     * Current user
     *
     * @var Identity
     */
    private $identity;

    /**
     * Use auto login
     *
     * @var bool
     */
    private $autoLogin;

    /**
     * Identity class name
     *
     * @var string
     */
    private $identityClass;

    /**
     * Set identity class name
     *
     * @param string $className
     * @return \pwf\components\authorization\Authorization
     */
    public function setIdentityClass($className)
    {
        $this->identityClass = $className;
        return $this;
    }

    /**
     * Get identity class name
     *
     * @return string
     */
    public function getIdentityClass()
    {
        return $this->identityClass;
    }

    /**
     * Set auto login
     * 
     * @param bool $autoLogin
     * @return \pwf\components\authorization\Authorization
     */
    public function setAutoLogin($autoLogin)
    {
        $this->autoLogin = $autoLogin;
        return $this;
    }

    /**
     * Is auto login
     *
     * @return bool
     */
    public function isAutoLogin()
    {
        return $this->autoLogin;
    }

    /**
     * Set current user
     *
     * @param Identity $user
     * @return Authorization
     */
    public function setIdentity(Identity $user, $remember = false)
    {
        $this->identity = $user;

        Application::$instance->getResponse()->addCookie(self::TOKEN_NAME,
            $user->getAuthToken(),
            $remember ? time() + 365 * 24 * 60 * 60 : null, '/');
        return $this;
    }

    /**
     * Get current user
     *
     * @return Identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Remove identity / logout
     *
     * @return Authorization
     */
    public function clearIdentity()
    {
        $this->identity = null;
        Application::$instance->getResponse()->removeCookie(self::TOKEN_NAME);
        return $this;
    }

    /**
     * Check is authorixed
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->identity !== null && $this->identity->getId() > 0;
    }

    /**
     * Load configuration
     *
     * @param array $config
     */
    public function loadConfiguration(array $config = [])
    {
        parent::loadConfiguration($config);
        if (isset($config['auto'])) {
            $this->setAutoLogin((bool) $config['auto']);
        }
        if (isset($config['identityClass'])) {
            $this->setIdentityClass($config['identityClass']);
        }
    }

    /**
     * Component initialization
     *
     * @return $this
     */
    public function init()
    {
        if ($this->isAutoLogin()) {
            $this->autoLogin();
        }
        return $this;
    }

    /**
     * Auto login
     */
    protected function autoLogin()
    {
        $token = Application::$instance->getRequest()->get(self::TOKEN_NAME);
        if ($token === null) {
            $token = Application::$instance->getRequest()->getCookie(self::TOKEN_NAME);
        }
        if ($token !== null) {
            $className = $this->getIdentityClass();
            $user      = (new $className)->getByAuthToken($token);
            $user->getId() > 0 ? $this->setIdentity($user) : $this->clearIdentity();
        }
    }
}