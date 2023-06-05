<?php
function is_logged_in()
{
    $ci = get_instance();
    if (!$ci->session->userdata('email')) {
        $ci->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        sorry you havent logged in!
              </div>');
        redirect('auth');
    } else {
        $role_id = $ci->session->userdata('role_id'); //kita login sebagai apa
        $menu = $ci->uri->segment(1); // baca url yang mau kita akses

        $queryMenu = $ci->db->get_where('user_menuNew', ['menu' => $menu])->row_array(); //baca ke query punya akses ga sebagai role apa
        $menu_id = $queryMenu['id'];

        // var_dump($menu);
        // die;

        $queryUserAccess = $ci->db->get_where('user_access_menu', [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ]);

        if ($queryUserAccess->num_rows() < 1) {
            redirect('auth/blocked');
        }
    }
}

function is_logged()
{
    $ci = get_instance();
    if (!$ci->session->userdata('email')) {
        $ci->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        sorry you havent logged in!
              </div>');
        redirect('auth');
    }
}

function access_menu()
{
    $ci = get_instance();
    if (!$ci->session->userdata('status')) {
        $ci->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
        Sorry you need to be logged in to access the menu personal data!
              </div>');
        redirect('auth/loginpersonaldata/""');
    }
}

function check_access($role_id, $menu_id)
{
    $ci = get_instance();

    $ci->db->where('role_id', $role_id);
    $ci->db->where('menu_id', $menu_id);
    $result = $ci->db->get('user_access_menu');

    if ($result->num_rows() > 0) {
        return "checked='checked'";
    }
}
