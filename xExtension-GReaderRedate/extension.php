<?php

class GReaderRedateExtension extends Minz_Extension
{
	public function init()
	{
		$this->registerTranslates();
		$this->registerHook('entry_before_display', array($this, 'handle_entry'));
	}

	public function handleConfigureAction()
	{
		if (Minz_Request::isPost())
		{
			FreshRSS_Context::$user_conf->gr_reeder_enable = Minz_Request::paramBoolean('gr_reeder');
			FreshRSS_Context::$user_conf->gr_easyrss_enable = Minz_Request::paramBoolean('gr_easyrss');
			FreshRSS_Context::$user_conf->gr_feedme_enable = Minz_Request::paramBoolean('gr_feedme');

			FreshRSS_Context::$user_conf->save();
		}
	}

	public function handle_entry($entry)
	{
		// This plug-in is only for GReader API.
		if (!isset($_SERVER['REQUEST_URI']) || strpos($_SERVER['REQUEST_URI'], '/api/greader.php') === FALSE)
			return $entry;

		// Fetch user agent.
		if (isset($_SERVER['HTTP_USER_AGENT']))
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
		else
			return $entry;

		// Match client.
		$redate = false;
		$matches = [];

		// > Match Reeder.
		if (!$redate && $this->isReederEnabled() && (preg_match('#Reeder/[0-9]+#', $user_agent, $matches) >= 1))
			$redate = true;

		// > Match FeedMe.
		if (!$redate && $this->isFeedMeEnabled() && (preg_match('#FeedMe/[0-9]+#', $user_agent, $matches) >= 1))
			$redate = true;
		
		// > Match EasyRSS. XXX it's *very* weak, but this thing identify iteself only by android...
		if (!$redate && $this->isEasyRSSEnabled() && (preg_match('#Android[ \t]+[0-9]+#', $user_agent, $matches) >= 1))
			$redate = true;

		// Replace date added in database by published date.
		if ($redate)
			$entry->_dateAdded($entry->date(true));

		return $entry;
	}

	public function isReederEnabled()
	{
		if (FreshRSS_Context::$user_conf->gr_reeder_enable !== null)
			return (bool)FreshRSS_Context::$user_conf->gr_reeder_enable;

		return true;
	}

	public function isEasyRSSEnabled()
	{
		if (FreshRSS_Context::$user_conf->gr_easyrss_enable !== null)
			return (bool)FreshRSS_Context::$user_conf->gr_easyrss_enable;

		return false;
	}
	
	public function isFeedMeEnabled()
	{
		if (FreshRSS_Context::$user_conf->gr_feedme_enable !== null)
			return (bool)FreshRSS_Context::$user_conf->gr_feedme_enable;

		return false;
	}
}

?>
