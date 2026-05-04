<?php
function authenticateAD(string $username, string $password): array|false {
    if (empty(trim($username)) || empty($password)) return false;
    if (!function_exists('ldap_connect')) return false;

    $ldap = @ldap_connect(LDAP_HOST, LDAP_PORT);
    if (!$ldap) return false;

    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 5);

    $bind = @ldap_bind($ldap, $username . '@' . LDAP_DOMAIN, $password);
    if (!$bind) { ldap_close($ldap); return false; }

    $filter = '(sAMAccountName=' . ldap_escape($username, '', LDAP_ESCAPE_FILTER) . ')';
    $attrs  = ['cn','givenName','sn','mail','department','title','memberOf'];
    $search = @ldap_search($ldap, LDAP_BASEDN, $filter, $attrs);
    $entries = $search ? ldap_get_entries($ldap, $search) : null;

    $info = [
        'username'  => $username,
        'fullname'  => $username,
        'email'     => '',
        'department'=> '',
        'groups'    => [],
    ];

    if ($entries && $entries['count'] > 0) {
        $e = $entries[0];
        $info['fullname']    = $e['cn'][0]         ?? $username;
        $info['firstname']   = $e['givenname'][0]  ?? '';
        $info['lastname']    = $e['sn'][0]         ?? '';
        $info['email']       = $e['mail'][0]       ?? '';
        $info['department']  = $e['department'][0] ?? '';
        if (!empty($e['memberof'])) {
            for ($i = 0; $i < $e['memberof']['count']; $i++) {
                preg_match('/^CN=([^,]+)/i', $e['memberof'][$i], $m);
                if (!empty($m[1])) $info['groups'][] = $m[1];
            }
        }
    }
    ldap_close($ldap);
    return $info;
}

function requireAdmin(): void {
    if (empty($_SESSION['admin'])) {
        header('Location: ' . ADMIN_URL . '/login.php');
        exit;
    }
    if (!empty($_SESSION['admin_time']) && (time() - $_SESSION['admin_time']) > SESSION_LIFETIME) {
        session_unset(); session_destroy();
        header('Location: ' . ADMIN_URL . '/login.php?timeout=1');
        exit;
    }
    $_SESSION['admin_time'] = time();
}
?>
