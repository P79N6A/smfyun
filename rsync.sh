/usr/bin/rsync -avz --exclude-from=rsync.exc . wz@180.76.243.74:/var/www/html/wz/ -e "ssh -p 22" --delete --chmod=Du=rwx,Dg=rwx,Do=rx,Fu=rw,Fg=rw,Fo=r
terminal-notifier
