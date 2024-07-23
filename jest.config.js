module.exports = {
    transform: {
        '^.+\\.vue$': 'vue-jest',
        '^.+\\.js$': 'jest-transform-stub',
    },
    moduleFileExtensions: ['js', 'json', 'vue'],
    testEnvironment: 'jest-environment-jsdom',
    moduleNameMapper: {
        '^@/(.*)$': '<rootDir>/src/$1',
    }
}
