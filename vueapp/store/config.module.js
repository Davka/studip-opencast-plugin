import ApiService from "@/common/api.service";

const initialState = {
    config_list: [],
    config: {
        'service_url' :      null,
        'service_user':      null,
        'service_password':  null,
        'settings': {
            'lti_consumerkey':    null,
            'lti_consumersecret': null,
            'debug':              null
        }
    }
};

const getters = {
    config_list(state) {
        return state.config_list;
    },
    config(state) {
        return state.config;
    }
};

export const state = { ...initialState };

export const actions = {
    async configListRead(context) {
        return new Promise(resolve => {
            ApiService.get('config')
                .then(({ data }) => {
                    context.commit('configListSet', data);
                    resolve(data);
                });
            });
    },

    async configListUpdate(context, params) {
        return  ApiService.put('config', {
            settings: params
        });
    },

    async configRead(context, id) {
        return ApiService.get('config/' + id)
            .then(({ data }) => {
                context.commit('configSet', data.config);
            });
    },

    async configDelete(context, id) {
        await ApiService.delete('config/' + id);
        context.dispatch('configListRead');
    },

    async configUpdate(context, params) {
        return ApiService.update('config', params.id, {
            config: params
        });
    },

    async configCreate(context, params) {
        return ApiService.post('config', {
            config: params
        }).then(({ data }) => {
            context.commit('configSet', data.config);
        });
    },

    configClear(context) {
        context.commit('configSet', {});
    },

    configListClear(context) {
        context.commit('configListSet', {});
    },
};

/* eslint no-param-reassign: ["error", { "props": false }] */
export const mutations = {
    configListSet(state, data) {
        state.config_list = data;
    },

    configSet(state, data) {
        if (data.settings === undefined || Array.isArray(data.settings)) {
            data.settings = {};
        }

        state.config = data;
    }
};

export default {
  state,
  actions,
  mutations,
  getters
};