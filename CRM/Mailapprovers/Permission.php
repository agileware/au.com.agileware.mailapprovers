<?php

class CRM_Mailapprovers_Permission extends CRM_Core_Permission_Temp {

  function check() {
    CRM_Core_Session::setStatus('Custom "Temp" Check', CRM_Mailapprovers_Permission);
  }
}