#!/bin/bash

INSTALL_DIR=.
LOGFILE='install.log'

ICON_INCOMPLETE_COLOUR=`tput setaf 1`
ICON_COMPLETE_COLOUR=`tput setaf 2`
TEXT_COLOUR=`tput setaf 2`
BAR_COLOUR_COMPLETE=`tput setaf 6`
BAR_COLOUR_REMAINING=`tput setaf 3`
ERROR_COLOUR=`tput setaf 1`
NO_COLOUR=`tput sgr0`

ICON_INCOMPLETE="${BAR_COLOUR_REMAINING}\xc2\xa2${NO_COLOUR}"
ICON_COMPLETE="${ICON_COMPLETE_COLOUR}\xcf\xbe${NO_COLOUR}"
ICON_ERROR="${ERROR_COLOUR}\xcf\xbf${NO_COLOUR}"

RELEASE='https://github.com/creode/csmt/raw/master/docs/downloads/csmt.phar'
RELEASE_KEY='https://raw.githubusercontent.com/creode/csmt/master/docs/downloads/csmt.phar.pubkey'
RELEASE_INDEX='https://raw.githubusercontent.com/creode/csmt/master/docs/downloads/index.php'
RELEASE_CONFIG='https://raw.githubusercontent.com/creode/csmt/master/docs/downloads/csmt.yml.example'

TMP_RELEASE_FILE=$(mktemp)
TMP_RELEASE_KEY_FILE=$(mktemp)
TMP_RELEASE_INDEX_FILE=$(mktemp)
TMP_RELEASE_CONFIG_FILE=$(mktemp)

TARGET_RELEASE_PATH="${INSTALL_DIR}/csmt.phar"
TARGET_RELEASE_KEY_PATH="${INSTALL_DIR}/csmt.phar.pubkey"
TARGET_RELEASE_INDEX_PATH="${INSTALL_DIR}/index.php"
TARGET_RELEASE_CONFIG_PATH="${INSTALL_DIR}/csmt.yml.example"



function err {
  log "FATAL ERROR: ${1}"
  completeLogEntry

  echo -ne "- ${ERROR_COLOUR}${1}${NO_COLOUR}\r"
  echo -ne "\n"
  echo -ne "- ${ICON_ERROR} csmt installation failed"
  echo -ne "\n"
  exit 1
} 

function log {
  touch ${INSTALL_DIR}/${LOGFILE}
  echo $1 >> ${INSTALL_DIR}/${LOGFILE}
}

function initLogEntry {
  log "============================================="
  log "Installation started at `date`"
  log "============================================="
}

function completeLogEntry {
  log "fin"
  log ""
  log ""
}

function progress {
  TOTAL=100
  COMPLETE=$1
  REMAINING=$(($TOTAL-$1))

  COMPLETE_CHAR_COUNT=$(($COMPLETE/5))
  REMAINING_CHAR_COUNT=$(($REMAINING/5))

  COMPLETE_CHARS=`eval printf "%0.sϾ" $(seq 0 $COMPLETE_CHAR_COUNT)`
  REMAINING_CHARS=`eval printf "%0.s." $(seq 0 $REMAINING_CHAR_COUNT)`

  echo -ne "- ${ICON_INCOMPLETE} ${BAR_COLOUR_COMPLETE}installing ${BAR_COLOUR_COMPLETE}${COMPLETE_CHARS:1}${BAR_COLOUR_REMAINING}${REMAINING_CHARS:1} ${TEXT_COLOUR}(${COMPLETE}%)${NO_COLOUR}\r"
}

function checkSystemRequirements {
  echo -ne "${BAR_COLOUR_REMAINING}Checking system requirements${NO_COLOUR}\r"

  PHP_VERSION=`php --version | head -n 1 | cut -d " " -f 2 | cut -c 1,3`

  if [ "$PHP_VERSION" -lt "55" ] ; then
    err "PHP Version must be at least 5.5 to use this tool"
  fi
}




mkdir -p ${INSTALL_DIR}

initLogEntry

progress 5

checkSystemRequirements

progress 10


log "Downloading latest csmt release key to $TMP_RELEASE_KEY_FILE"

if curl -LsSo $TMP_RELEASE_KEY_FILE $RELEASE_KEY ; then
  progress 20

  log "Copying to $TARGET_RELEASE_KEY_PATH"
  if cp $TMP_RELEASE_KEY_FILE $TARGET_RELEASE_KEY_PATH ; then
    progress 30
    log "csmt key was successfully installed."
  else
    err "Error when copying csmt release to ${TARGET_RELEASE_KEY_PATH}"
  fi

else
  err "Error when downloading csmt release from ${RELEASE_KEY}"
fi



log "Downloading latest csmt release to $TMP_RELEASE_FILE"

if curl -LsSo $TMP_RELEASE_FILE $RELEASE ; then
  progress 40

  log "Copying to $TARGET_RELEASE_PATH"
  if cp $TMP_RELEASE_FILE $TARGET_RELEASE_PATH ; then
    progress 50

    log "Making executable"
    if chmod +x $TARGET_RELEASE_PATH ; then
      progress 60
      log "csmt.phar successfully installed."
    else
      err "Error when granting execute permissions on ${TARGET_RELEASE_PATH}"
    fi

  else
    err "Error when copying csmt release to ${TARGET_RELEASE_PATH}"
  fi

else
  err "Error when downloading csmt release from ${RELEASE}"
fi



log "Downloading latest csmt index release to $TMP_RELEASE_INDEX_FILE"

if curl -LsSo $TMP_RELEASE_INDEX_FILE $RELEASE_INDEX ; then
  progress 70

  log "Copying to $TARGET_RELEASE_PATH"
  if cp $TMP_RELEASE_INDEX_FILE $TARGET_RELEASE_INDEX_PATH ; then
    progress 80
    log "index.php successfully installed."
  else
    err "Error when copying csmt index release to ${TARGET_RELEASE_INDEX_PATH}"
  fi

else
  err "Error when downloading csmt index release from ${RELEASE_INDEX}"
fi



log "Downloading latest csmt example config to $TMP_RELEASE_CONFIG_FILE"

if curl -LsSo $TMP_RELEASE_CONFIG_FILE $RELEASE_CONFIG ; then
  progress 90

  log "Copying to $TARGET_RELEASE_PATH"
  if cp $TMP_RELEASE_CONFIG_FILE $TARGET_RELEASE_CONFIG_PATH ; then
    progress 95
    log "example config file successfully installed."
  else
    err "Error when copying csmt example config to ${TARGET_RELEASE_CONFIG_PATH}"
  fi

else
  err "Error when downloading csmt example config from ${RELEASE_CONFIG}" 
fi

echo -ne "Setting permissions on files to match ../index.php"
if [ -f ../index.php ]; then
  log "setting permissions on files to match ../index.php"
  chown --reference=../index.php ${TARGET_RELEASE_PATH} ${TARGET_RELEASE_KEY_PATH} ${TARGET_RELEASE_INDEX_PATH} ${TARGET_RELEASE_CONFIG_PATH}
  chmod --reference=../index.php ${TARGET_RELEASE_PATH} ${TARGET_RELEASE_KEY_PATH} ${TARGET_RELEASE_INDEX_PATH} ${TARGET_RELEASE_CONFIG_PATH}
else
  log "no ../index.php file found, not setting permissions"
  echo "${ERROR_COLOUR}You will need to set file ownership/permissions so that the web server can access the following files: \n"
  echo "${TARGET_RELEASE_PATH}"
  echo "${TARGET_RELEASE_KEY_PATH}"
  echo "${TARGET_RELEASE_INDEX_PATH}"
  echo "${TARGET_RELEASE_CONFIG_PATH}${NO_COLOUR}"
fi


progress 100

echo -ne "- ${ICON_COMPLETE} csmt was successfully installed. Run ${TEXT_COLOUR}php csmt.phar${NO_COLOUR} for usage"
echo -ne "\n"

log "Installation completed successfully"

completeLogEntry

exit 0
