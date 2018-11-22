#!/bin/bash
/usr/bin/rsync -avz --exclude-from=rsync.exc . dev@123.57.230.177: -e "ssh -p 222" --delete --chmod=Du=rwx,Dg=rwx,Do=rx,Fu=rw,Fg=rw,Fo=r
terminal-notifier -message "Rsync done."

exit 0

