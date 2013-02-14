<?php

class Pages extends CI_Controller {

    public function view($page = 'home') {
        if (!file_exists('application/views/pages/' . $page . '.php')) {
            // Whoops, we don't have a page for that!
            show_404();
        }

//
//        $this->load->library('codeigniter-ninjauth/ninjauth', array('provider' => 'linkedin'), 'NinjAuthCI');
//        $this->load->library($this->NinjAuthCI->getLibraryName(), array(), 'cioauth');
//
//        die('test');
//

        $data['title'] = ucfirst($page); // Capitalize the first letter

        $this->load->view('templates/header', $data);
        $this->load->view('pages/' . $page, $data);
        $this->load->view('templates/footer', $data);
    }

}