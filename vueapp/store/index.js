import { createStore } from 'vuex';

import error from "./error.module";
import config from "./config.module";
import messages from "./messages.module";
import lti from "./lti.module";
import opencast from "./opencast.module";
import videos from "./videos.module";
import playlists from "./playlists.module";
import courses from "./courses.module";
import log from "./log.module";


export default createStore({
  modules: {
    error,
    config,
    messages,
    lti,
    opencast,
    videos,
    playlists,
    courses,
    log
  }
});