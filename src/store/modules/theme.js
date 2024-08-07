/* eslint-disable no-console */
import { Theme } from '../../entities/index.js'
import { defineStore } from 'pinia'

export const useThemeStore = defineStore(
    'theme', {
        state: () => ({
            themeItem: false,
            themeList: [],
        }),
    actions: {
        setThemeItem(themeItem) {
            this.themeItem = themeItem && new Theme(themeItem)
            console.log('Active theme item set to ' + themeItem && themeItem?.id)
        },
        setThemeList(themeList) {
            this.themeList = themeList.map(
                (themeItem) => new Theme(themeItem),
            )
         console.log('Theme list set to ' + themeList.length + ' items')
        },
        /* istanbul ignore next */ // ignore this for Jest until moved into a service
        async refreshThemeList(search = null) {
            // @todo this might belong in a service?
            let endpoint = '/index.php/apps/opencatalogi/api/themes'
            if (search !== null && search !== '') {
                endpoint = endpoint + '?_search=' + search
            }
            return fetch(
                endpoint, {
                    method: 'GET',
                }
            )
                .then(
                    (response) => {
                        response.json().then(
                        (data) => {
                                this.setThemeList(data.results)
                            }
                    )
                    }
                )
                .catch(
                    (err) => {
                        console.error(err)
                    }
                )
        },
        },
    }
)
