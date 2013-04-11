for SRC in `find . -depth`
 do DST=`dirname "${SRC}"`/`basename "${SRC}" | sed -e 's/^./\U&/'`;
     if [ "${SRC}" != "${DST}" ]
     then
       [ ! -e "${DST}" ] && mv -T "${SRC}" "${DST}" || echo "${SRC} was not renamed"
     fi
done
