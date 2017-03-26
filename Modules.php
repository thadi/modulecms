<?php

class Modules{

	private $modules = array();

    /**
	 * Returns the AbstractModule
	 * @return AbstractModule
	 */
	public function getAbstractModule(){
		if(!empty($this->modules["abstract"])){
			return $this->modules["abstract"];
		}
		$this->modules["abstract"] = new AbstractModule();			
		return $this->modules["abstract"];
	}

    /**
	 * Returns the AdminModule
	 * @return AdminModule
	 */
	public function getAdminModule(){
		if(!empty($this->modules["admin"])){
			return $this->modules["admin"];
		}
		$this->modules["admin"] = new AdminModule();			
		return $this->modules["admin"];
	}

    /**
	 * Returns the FileModule
	 * @return FileModule
	 */
	public function getFileModule(){
		if(!empty($this->modules["file"])){
			return $this->modules["file"];
		}
		$this->modules["file"] = new FileModule();			
		return $this->modules["file"];
	}

    /**
	 * Returns the FilesystemModule
	 * @return FilesystemModule
	 */
	public function getFilesystemModule(){
		if(!empty($this->modules["filesystem"])){
			return $this->modules["filesystem"];
		}
		$this->modules["filesystem"] = new FilesystemModule();			
		return $this->modules["filesystem"];
	}

    /**
	 * Returns the LogModule
	 * @return LogModule
	 */
	public function getLogModule(){
		if(!empty($this->modules["log"])){
			return $this->modules["log"];
		}
		$this->modules["log"] = new LogModule();			
		return $this->modules["log"];
	}

    /**
	 * Returns the PersistenzModule
	 * @return PersistenzModule
	 */
	public function getPersistenzModule(){
		if(!empty($this->modules["persistenz"])){
			return $this->modules["persistenz"];
		}
		$this->modules["persistenz"] = new PersistenzModule();			
		return $this->modules["persistenz"];
	}

    /**
	 * Returns the RequestModule
	 * @return RequestModule
	 */
	public function getRequestModule(){
		if(!empty($this->modules["request"])){
			return $this->modules["request"];
		}
		$this->modules["request"] = new RequestModule();			
		return $this->modules["request"];
	}

    /**
	 * Returns the SessionModule
	 * @return SessionModule
	 */
	public function getSessionModule(){
		if(!empty($this->modules["session"])){
			return $this->modules["session"];
		}
		$this->modules["session"] = new SessionModule();			
		return $this->modules["session"];
	}

    /**
	 * Returns the SettingModule
	 * @return SettingModule
	 */
	public function getSettingModule(){
		if(!empty($this->modules["setting"])){
			return $this->modules["setting"];
		}
		$this->modules["setting"] = new SettingModule();			
		return $this->modules["setting"];
	}

    /**
	 * Returns the UriModule
	 * @return UriModule
	 */
	public function getUriModule(){
		if(!empty($this->modules["uri"])){
			return $this->modules["uri"];
		}
		$this->modules["uri"] = new UriModule();			
		return $this->modules["uri"];
	}

    /**
	 * Returns the UserModule
	 * @return UserModule
	 */
	public function getUserModule(){
		if(!empty($this->modules["user"])){
			return $this->modules["user"];
		}
		$this->modules["user"] = new UserModule();			
		return $this->modules["user"];
	}
}
?>