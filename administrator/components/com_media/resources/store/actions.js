import {api} from "../app/Api";
import * as types from "./mutation-types";

// Actions are similar to mutations, the difference being that:
// - Instead of mutating the state, actions commit mutations.
// - Actions can contain arbitrary asynchronous operations.

/**
 * Get contents of a directory from the api
 * @param commit
 * @param dir
 */
export const getContents = ({commit}, dir) => {
    api.getContents(dir)
        .then((contents) => {
            commit(types.LOAD_CONTENTS_SUCCESS, contents);
            commit(types.SELECT_DIRECTORY, dir);
        })
        .catch(error => {
            // TODO error handling
            console.log("error", error);
        });
}

/**
 * Create a new folder
 * @param commit
 * @param payload object with the new folder name and its parent directory
 */
export const createFolder = ({commit}, payload) => {
    api.createFolder(payload.name, payload.parent)
        .then((contents) => {
            console.log(contents);
        })
        .catch(error => {
            // TODO error handling
            console.log("error", error);
        })
}

