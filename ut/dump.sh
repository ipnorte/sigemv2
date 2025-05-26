#!/bin/bash
COMMIT_COUNT=0
COMMIT_LIMIT=10
for DBTB in `cat ListOfTables.txt`
do
    DB=`echo ${DBTB} | sed 's/\./ /g' | awk '{print $1}'`
    TB=`echo ${DBTB} | sed 's/\./ /g' | awk '{print $2}'`
    mysqldump -h... -u... -p... --hex-blob --triggers ${DB} ${TB} | gzip > ${DB}_${TB}.sql.gz &
    (( COMMIT_COUNT++ ))
    if [ ${COMMIT_COUNT} -eq ${COMMIT_LIMIT} ]
    then
        COMMIT_COUNT=0
        wait
    fi
done
if [ ${COMMIT_COUNT} -gt 0 ]
then
    wait
fi
