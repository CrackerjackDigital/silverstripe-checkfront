<?php

class CheckfrontAPISessionEndpoint extends CheckfrontAPIEndpoint {

    /**
     * Clear the current checkfront session and data from local session.
     * @return void
     */
    public function clearSession() {
        $session = CheckfrontModule::session();

        if ($sessionID = $session->getID()) {
            CheckfrontModule::api()->post(
                new CheckfrontAPIRequest(
                    'session/clear',
                    array(
                        'session_id' => $sessionID
                    )
                )
            );
        }
        $session->clear('data');

    }
    /**
     * End the current session and clear local session
     * @return void
     */
    public function endSession() {
        $session = CheckfrontModule::session();

        if ($sessionID = $session->getID()) {
            CheckfrontModule::api()->post(
                new CheckfrontAPIRequest(
                    'session/end',
                    array(
                        'session_id' => $sessionID
                    )
                )
            );
        }
        $session->clear(null);

    }
}