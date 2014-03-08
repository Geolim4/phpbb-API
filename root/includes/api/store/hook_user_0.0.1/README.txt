#####[EN][English]------------------------------
Your language file should have the same suffix than your hook, e.g:
Here the suffix is "display_ini"
Hook: 
root/includes/api/hooks/hook_api_display_ini.php

Lang: 
root/language/en/mods/hooks/info_acp_hook_display_ini.php

So if the hook "hook_api_{display_ini}.php" is deleted from the acp, PHP will also delete this file:
root/language/en/mods/hooks/info_acp_hook_{display_ini}.php

For security reasons, phpBB API will ignore all file that does not match with the example above:
Ignored:

root/includes/bad_file.phproot/bad_file.php
root/language/bad_file.php
Accepted:

root/includes/api/hooks/hook_api_display_ini.php
root/language/en/mods/hooks/info_acp_hook_display_ini.php

#####[FR][French]------------------------------
Votre fichier de langue doit avoir le même suffixe que votre hook, ex:
Ici le suffixe est "display_ini"
Hook: 
root/includes/api/hooks/hook_api_display_ini.php

Langue: 
root/language/en/mods/hooks/info_acp_hook_display_ini.php

Donc si le hook "hook_api_{display_ini}.php" est supprimé depuis l'ACP, PHP va aussi supprimer ce fichier:
root/language/en/mods/hooks/info_acp_hook_{display_ini}.php

Pour des raisons de sécurité, phpBB API will ignorera tout les fichiers qui ne correspondent pas à l'exemple ci-dessus:

Ignoré:
root/includes/mauvais_fichier.phproot/mauvais_fichier.php
root/language/mauvais_fichier.php

Accepté:
root/includes/api/hooks/hook_api_display_ini.php
root/language/fr/mods/hooks/info_acp_hook_display_ini.php