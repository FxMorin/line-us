import os
import time
time.sleep(3)
sudoPass = 'pattison'
command = "sudo /Applications/XAMPP/xamppfiles/bin/apachectl -k restart"
p = os.popen('echo %s|sudo -S %s' % (sudoPass, command))
